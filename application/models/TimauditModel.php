<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Tim auditor pada satu audit - ketua + anggota.
 *
 * Tabel : mutu_auditteams (dibuat dari database/tim_audit.sql atau menu
 *         PPM > Migrasi bila tersedia)
 * Relasi: mutu_auditteams.audit_id -> mutu_audit.audit_id
 *         mutu_auditteams.users_id -> users.users_id (role AUDITOR)
 *
 * Kolom lama mutu_audit.auditor_id tetap diisi dengan users_id KETUA,
 * sehingga fitur yang belum mengenal tim (ekspor Excel, saringan lama)
 * tidak berubah.
 *
 * Bila tabelnya belum ada, siap() mengembalikan FALSE dan seluruh
 * pemanggil memakai perilaku lama (satu auditor), jadi aplikasi tetap
 * berjalan meski berkas SQL belum diimpor.
 */
class TimauditModel extends CI_Model {

    var $tabel   = 'mutu_auditteams';
    var $t_audit = 'mutu_audit';
    var $t_users = 'users';

    function __construct() {
        parent::__construct();
    }

    /** Tabel tim berikut kolom utamanya sudah tersedia? */
    public function siap() {
        return $this->db->table_exists($this->tabel)
            && $this->db->field_exists('audit_id', $this->tabel)
            && $this->db->field_exists('users_id', $this->tabel);
    }

    /* =====================================================================
     | BACA
     * ================================================================== */

    /**
     * Susunan tim beberapa audit sekaligus (satu kueri untuk seluruh
     * halaman tabel): audit_id => daftar anggota, ketua lebih dahulu.
     *
     * @param  array $audit_ids
     * @return array
     */
    public function peta($audit_ids) {
        $hasil = array();
        if (!$this->siap() || empty($audit_ids)) {
            return $hasil;
        }

        $ids = array();
        foreach ((array) $audit_ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $ids[] = $id;
            }
        }
        if (empty($ids)) {
            return $hasil;
        }

        $this->db->select('t.audit_id, t.users_id, t.tim_peran, t.tim_urut');
        $this->db->select('u.nama, u.unitkerja');
        $this->db->from($this->tabel . ' t');
        $this->db->join($this->t_users . ' u', 'u.users_id = t.users_id', 'left');
        $this->db->where_in('t.audit_id', $ids);
        /* Ketua (tim_urut 0) tampil lebih dahulu, lalu anggota sesuai
           urutan pemilihan. */
        $this->db->order_by('t.tim_urut', 'ASC');
        $this->db->order_by('t.tim_id', 'ASC');

        $baris = $this->db->get()->result_array();
        /* Peta manual supaya nama tabel tetap bisa memakai awalan apa pun. */
        foreach ($baris as $b) {
            $key = (int) $b['audit_id'];
            $hasil[$key][] = array(
                'users_id'  => (int) $b['users_id'],
                'nama'      => isset($b['nama']) ? $b['nama'] : '',
                'unitkerja' => isset($b['unitkerja']) ? $b['unitkerja'] : '',
                'tim_peran' => $b['tim_peran'],
                'tim_urut'  => (int) $b['tim_urut'],
            );
        }

        return $hasil;
    }

    /** Anggota tim satu audit (ketua lebih dahulu). */
    public function daftar($audit_id) {
        $peta = $this->peta(array($audit_id));
        return isset($peta[(int) $audit_id]) ? $peta[(int) $audit_id] : array();
    }

    /** users_id ketua tim, atau NULL bila audit belum punya tim. */
    public function ketuaId($audit_id) {
        foreach ($this->daftar($audit_id) as $b) {
            if ($b['tim_peran'] === 'KETUA') {
                return (int) $b['users_id'];
            }
        }
        return NULL;
    }

    /**
     * Daftar audit_id tempat pengguna ini terdaftar sebagai anggota tim.
     * Dipakai menyusun saringan "audit saya" pada laman auditor, supaya
     * anggota tim ikut melihat dan menilai auditnya.
     *
     * @param  int $users_id
     * @return array daftar audit_id
     */
    public function auditUntuk($users_id) {
        $hasil = array();
        if (!$this->siap() || empty($users_id)) {
            return $hasil;
        }

        $this->db->select('audit_id');
        $this->db->where('users_id', (int) $users_id);
        foreach ($this->db->get($this->tabel)->result_array() as $r) {
            $hasil[] = (int) $r['audit_id'];
        }

        return $hasil;
    }

    /**
     * Terapkan saringan "audit saya" pada kueri yang sedang disusun:
     * audit yang auditornya pengguna ini (ketua tim) DITAMBAH audit
     * tempat ia terdaftar pada tim auditor.
     *
     * Dipakai laman Daftar Audit, rincian audit, Daftar Tilik, dan PTK
     * milik auditor supaya anggota tim ikut mengerjakan auditnya.
     *
     * @param string $alias    alias tabel mutu_audit pada kueri
     * @param string $kolom    'auditor_id' atau 'auditee_id'
     * @param int    $users_id NULL = pengguna yang sedang masuk
     */
    public function saring($alias, $kolom = 'auditor_id', $users_id = NULL) {
        if ($users_id === NULL) {
            $users_id = $this->session->userdata('users_id');
        }
        if (!isset($users_id)) {
            return;
        }

        $tim = $this->siap() ? $this->auditUntuk($users_id) : array();
        if (empty($tim)) {
            $this->db->where($alias . '.' . $kolom, $users_id);
            return;
        }

        $this->db->group_start();
        $this->db->where($alias . '.' . $kolom, $users_id);
        $this->db->or_where_in($alias . '.audit_id', $tim);
        $this->db->group_end();
    }

    /* =====================================================================
     | UBAH
     * ================================================================== */

    /**
     * Simpan susunan tim satu audit.
     *
     * Baris lama audit ini dibuang lalu ditulis ulang, jadi daftar yang
     * dikirim adalah susunan yang berlaku. Ketua selalu tercatat walau
     * id-nya tidak ikut pada $daftar_id.
     *
     * @param  int   $audit_id
     * @param  array $daftar_id users_id seluruh auditor (ketua ikut di sini)
     * @param  int   $ketua_id  users_id ketua tim
     * @return bool
     */
    public function simpan($audit_id, $daftar_id, $ketua_id) {
        if (!$this->siap()) {
            return FALSE;
        }

        $audit_id = (int) $audit_id;
        $ketua_id = (int) $ketua_id;
        if ($audit_id < 1 || $ketua_id < 1) {
            return FALSE;
        }

        $this->db->trans_start();

        $this->db->where('audit_id', $audit_id);
        $this->db->delete($this->tabel);

        $urut      = 1;
        $ketua_ok  = FALSE;
        $dipakai   = array();

        foreach ((array) $daftar_id as $uid) {
            $uid = (int) $uid;
            if ($uid < 1 || isset($dipakai[$uid])) {
                continue;
            }
            $dipakai[$uid] = TRUE;

            $ketua   = ($uid === $ketua_id);
            $sekarang = date('Y-m-d H:i:s');
            $this->db->insert($this->tabel, array(
                'audit_id'   => $audit_id,
                'users_id'   => $uid,
                'tim_peran'  => $ketua ? 'KETUA' : 'ANGGOTA',
                'tim_urut'   => $ketua ? 0 : $urut,
                'tim_create' => $sekarang,
            ));

            if ($ketua) {
                $ketua_ok = TRUE;
            } else {
                $urut++;
            }
        }

        /* Penjagaan: ketua selalu tercatat. */
        if (!$ketua_ok) {
            $this->db->insert($this->tabel, array(
                'audit_id'   => $audit_id,
                'users_id'   => $ketua_id,
                'tim_peran'  => 'KETUA',
                'tim_urut'   => 0,
                'tim_create' => date('Y-m-d H:i:s'),
            ));
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Baris tim dibuang bersama audit yang dihapus. */
    public function hapusAudit($audit_id) {
        if (!$this->siap()) {
            return FALSE;
        }

        $this->db->where('audit_id', (int) $audit_id);
        return $this->db->delete($this->tabel);
    }

    /* =====================================================================
     | BANTUAN TAMPILAN
     * ================================================================== */

    /**
     * Teks ringkas daftar tim untuk kolom tabel, misalnya
     * "Budi Santoso (Ketua), Sari, Joko".
     *
     * @param  array  $baris    hasil peta()/daftar()
     * @param  string $cadangan teks bila tim belum ada (mutu_audit.auditor)
     * @return string
     */
    public function teks($baris, $cadangan = '') {
        if (empty($baris) || !is_array($baris)) {
            return (string) $cadangan;
        }

        $nama = array();
        foreach ($baris as $b) {
            $teks = isset($b['nama']) ? $b['nama'] : '';
            if ($teks === '' && isset($b['users_id'])) {
                $teks = 'Auditor #' . (int) $b['users_id'];
            }
            if (isset($b['tim_peran']) && $b['tim_peran'] === 'KETUA') {
                $teks .= ' (Ketua)';
            }
            $nama[] = $teks;
        }

        return implode(', ', $nama);
    }
}
