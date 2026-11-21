<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Kesimpulan audit - Bagian VII Laporan Audit Mutu Internal (AMI).
 *
 * Tiga pertanyaan kesimpulan dijawab lewat modal sebelum Laporan AMI
 * diunduh (bila audit ini belum pernah diisi). Jawabannya tersimpan pada
 * tabel `auditkesimpulan` (mutu_auditkesimpulan) dan dituliskan pada
 * berkas Excel Laporan AMI.
 *
 * Bila tabelnya belum diimpor (lihat database/kesimpulan_audit.sql),
 * siap() bernilai FALSE dan unduhan ditolak sampai tabel diimpor.
 */
class KesimpulanModel extends CI_Model {

    /** Nama tabel tanpa prefix (prefix dibaca dari konfigurasi CI). */
    private $tabel = 'auditkesimpulan';

    function __construct() {
        parent::__construct();
    }

    /** Tabel kesimpulan sudah tersedia? */
    public function siap() {
        return $this->db->table_exists($this->tabel);
    }

    /**
     * Kesimpulan satu audit, atau NULL bila belum pernah diisi.
     *
     * @param  int $audit_id
     * @return array|NULL
     */
    public function ambil($audit_id) {
        if (!$this->siap()) {
            return NULL;
        }

        $this->db->where('audit_id', (int) $audit_id);
        $baris = $this->db->get($this->tabel)->row_array();
        return $baris ? $baris : NULL;
    }

    /**
     * Apakah kesimpulan audit ini sudah pernah diisi (ketiga pertanyaan
     * terisi)? Dipakai untuk memutuskan apakah modal perlu ditampilkan
     * sebelum unduhan.
     *
     * @param  int $audit_id
     * @return bool
     */
    public function sudahDiisi($audit_id) {
        $k = $this->ambil($audit_id);
        if (!$k) {
            return FALSE;
        }

        return trim((string) $k['kesimpulan1']) !== ''
            && trim((string) $k['kesimpulan2']) !== ''
            && trim((string) $k['kesimpulan3']) !== '';
    }

    /**
     * Simpan (atau perbarui) jawaban kesimpulan satu audit.
     *
     * @param  int   $audit_id
     * @param  array $data kunci kesimpulan1, kesimpulan1_lainnya,
     *                      kesimpulan2, kesimpulan2_lainnya, kesimpulan3
     * @return bool
     */
    public function simpan($audit_id, $data) {
        if (!$this->siap()) {
            return FALSE;
        }

        $audit_id = (int) $audit_id;
        $baris = array(
            'audit_id'            => $audit_id,
            'kesimpulan1'         => isset($data['kesimpulan1']) ? $data['kesimpulan1'] : NULL,
            'kesimpulan1_lainnya' => isset($data['kesimpulan1_lainnya']) ? $data['kesimpulan1_lainnya'] : NULL,
            'kesimpulan2'         => isset($data['kesimpulan2']) ? $data['kesimpulan2'] : NULL,
            'kesimpulan2_lainnya' => isset($data['kesimpulan2_lainnya']) ? $data['kesimpulan2_lainnya'] : NULL,
            'kesimpulan3'         => isset($data['kesimpulan3']) ? $data['kesimpulan3'] : NULL,
        );

        if (isset($this->session) && $this->session->userdata('users_id')) {
            $baris['users_id'] = (int) $this->session->userdata('users_id');
        }

        $ada = $this->ambil($audit_id);
        if ($ada) {
            $this->db->where('audit_id', $audit_id);
            return (bool) $this->db->update($this->tabel, $baris);
        }

        return (bool) $this->db->insert($this->tabel, $baris);
    }
}
