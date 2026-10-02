<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Lampiran jawaban per lingkup (butir) audit.
 *
 * Tabel : mutu_lampiran (database/lampiran_lingkup.sql)
 * Berkas: filedata/lampiran/<lampiran_nama>
 *
 * Satu lingkup pada satu audit boleh memiliki banyak lampiran. Kolom
 * `lampiran_nama` berisi nama berkas di server (acak, dari encrypt_name),
 * sedangkan `lampiran_asli` nama berkas asli dari pengunggah.
 */
class LampiranModel extends CI_Model {

    var $tabel  = 'lampiran';
    var $folder = 'filedata/lampiran';

    function __construct() {
        parent::__construct();
    }

    /** Tabel lampiran sudah ada? (fitur lampiran opsional) */
    function siap() {
        return $this->db->table_exists($this->tabel);
    }

    /** Kolom lampiran penanda butir: dtjwb_id (butir tilik) atau lingkup_id. */
    function kolomButir() {
        if (!$this->siap()) {
            return NULL;
        }
        return $this->db->field_exists('dtjwb_id', $this->tabel) ? 'dtjwb_id' : 'lingkup_id';
    }

    /** Lampiran sudah menempel pada butir tilik (auditjawabdetail)? */
    function butirSiap() {
        return $this->siap() && $this->db->field_exists('dtjwb_id', $this->tabel);
    }

    /**
     * Buat tabel lampiran (aman dijalankan berulang kali). Dipakai tombol
     * penyiapan di halaman migrasi admin dan saat pengujian.
     */
    function install() {
        $this->load->dbforge();

        if (!$this->db->table_exists($this->tabel)) {
            $this->dbforge->add_field(array(
                'lampiran_id'     => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'audit_id'        => array('type' => 'INT', 'constraint' => 11),
                'dtjwb_id'        => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
                'lingkup_id'      => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
                'users_id'        => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
                'lampiran_nama'   => array('type' => 'VARCHAR', 'constraint' => 255),
                'lampiran_asli'   => array('type' => 'VARCHAR', 'constraint' => 255),
                'lampiran_tipe'   => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE),
                'lampiran_ukuran' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'lampiran_create' => array('type' => 'DATETIME', 'null' => TRUE),
            ));
            $this->dbforge->add_key('lampiran_id', TRUE);
            $this->dbforge->add_key('audit_id');
            $this->dbforge->add_key('lingkup_id');
            $this->dbforge->create_table($this->tabel, TRUE);
        }

        /* Tabel lama: tambahkan kolom butir tilik dan longgarkan lingkup_id
           (kolom itu NOT NULL pada versi pertama). */
        if ($this->siap() && !$this->db->field_exists('dtjwb_id', $this->tabel)) {
            if (in_array($this->db->dbdriver, array('sqlite', 'sqlite3'), TRUE)) {
                $this->db->query('ALTER TABLE ' . $this->db->protect_identifiers($this->tabel, TRUE)
                    . ' ADD COLUMN dtjwb_id INTEGER');
            } else {
                $this->load->dbforge();
                $this->dbforge->add_column($this->tabel, array(
                    'dtjwb_id' => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE, 'after' => 'audit_id'),
                ));
                if ($this->db->field_exists('lingkup_id', $this->tabel)) {
                    /* Agar lampiran butir tilik bisa disimpan tanpa lingkup_id. */
                    $this->dbforge->modify_column($this->tabel, array(
                        'lingkup_id' => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
                    ));
                }
            }
        }

        return $this->siap();
    }

    /** Folder penyimpanan berkas lampiran (dibuat bila belum ada). */
    function folder($absolute = TRUE) {
        $path = ($absolute ? FCPATH : '') . $this->folder;
        if (!is_dir($path)) {
            @mkdir($path, 0777, TRUE);
        }
        return rtrim($path, '/\\') . '/';
    }

    /** Alamat unduh satu lampiran (berkas disajikan apa adanya). */
    function url($nama) {
        return base_url($this->folder . '/' . rawurlencode($nama));
    }

    /* ==================================================================
       Baca
       ================================================================== */

    /** Lampiran satu butir pada satu audit, terlama lebih dahulu. */
    function daftar($audit_id, $butir_id) {
        $kolom = $this->kolomButir();
        if (!$kolom) {
            return array();
        }
        $this->db->where('audit_id', $audit_id);
        $this->db->where($kolom, $butir_id);
        $this->db->order_by('lampiran_id', 'ASC');
        $hasil = array();
        foreach ($this->db->get($this->tabel)->result_array() as $row) {
            $row['url']         = $this->url($row['lampiran_nama']);
            $row['ukuran_teks'] = $this->ukuranTeks($row['lampiran_ukuran']);
            $hasil[] = $row;
        }
        return $hasil;
    }

    /** Satu lampiran berdasarkan id. */
    function satu($lampiran_id) {
        if (!$this->siap() || empty($lampiran_id)) {
            return NULL;
        }
        $this->db->where('lampiran_id', $lampiran_id);
        return $this->db->get($this->tabel)->row_array();
    }

    /**
     * Peta [id butir => daftar lampiran] untuk satu audit.
     * Butir = dtjwb_id (butir tilik) bila kolomnya ada, jika tidak
     * lingkup_id (data lama). Dipakai halaman detail audit.
     */
    function peta($audit_id) {
        $hasil = array();
        $kolom = $this->kolomButir();
        if (!$kolom) {
            return $hasil;
        }

        $this->db->where('audit_id', $audit_id);
        $this->db->order_by('lampiran_id', 'ASC');
        foreach ($this->db->get($this->tabel)->result_array() as $row) {
            $row['url']         = $this->url($row['lampiran_nama']);
            $row['ukuran_teks'] = $this->ukuranTeks($row['lampiran_ukuran']);
            $hasil[(int) $row[$kolom]][] = $row;
        }
        return $hasil;
    }

    /** Jumlah lampiran satu butir (atau seluruh audit). */
    function hitung($audit_id, $butir_id = NULL) {
        $kolom = $this->kolomButir();
        if (!$kolom) {
            return 0;
        }
        $this->db->where('audit_id', $audit_id);
        if ($butir_id !== NULL) {
            $this->db->where($kolom, $butir_id);
        }
        return $this->db->count_all_results($this->tabel);
    }

    /* ==================================================================
       Tulis
       ================================================================== */

    /** Catat satu berkas yang sudah dipindahkan ke folder lampiran. */
    function tambah($audit_id, $butir_id, $berkas) {
        $kolom = $this->kolomButir();
        if (!$kolom) {
            return NULL;
        }

        $isi = array(
            'audit_id'        => $audit_id,
            $kolom            => $butir_id,
            'users_id'        => $this->session->userdata('users_id'),
            'lampiran_nama'   => $berkas['file_name'],
            'lampiran_asli'   => $berkas['orig_name'],
            'lampiran_tipe'   => isset($berkas['file_ext']) ? ltrim($berkas['file_ext'], '.') : NULL,
            'lampiran_ukuran' => isset($berkas['file_size']) ? (int) round($berkas['file_size'] * 1024) : 0,
            'lampiran_create' => date('Y-m-d H:i:s'),
        );

        /* Tabel lama punya lingkup_id NOT NULL - isi kosong bila kolom butir
           tilik sudah dipakai supaya penyimpanan tidak gagal. */
        if ($kolom === 'dtjwb_id' && $this->db->field_exists('lingkup_id', $this->tabel)) {
            $isi['lingkup_id'] = 0;
        }

        $this->db->insert($this->tabel, $isi);

        return $this->db->insert_id();
    }

    /** Hapus satu lampiran beserta berkas fisiknya. */
    function hapus($lampiran_id) {
        $row = $this->satu($lampiran_id);
        if (!$row) {
            return FALSE;
        }

        $berkas = FCPATH . $this->folder . '/' . $row['lampiran_nama'];
        if (is_file($berkas)) {
            @unlink($berkas);
        }

        $this->db->where('lampiran_id', $lampiran_id);
        $this->db->delete($this->tabel);
        return TRUE;
    }

    /** Ukuran berkas dalam bentuk teks (mis. 512 KB, 1,2 MB). */
    function ukuranTeks($byte) {
        $byte = (int) $byte;
        if ($byte < 1024) {
            return $byte . ' B';
        }
        if ($byte < 1024 * 1024) {
            return round($byte / 1024, 0) . ' KB';
        }
        return number_format($byte / (1024 * 1024), 1, ',', '.') . ' MB';
    }
}
