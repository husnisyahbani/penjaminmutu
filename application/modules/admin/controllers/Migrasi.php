<?php

/**
 * Migrasi lingkup audit (sekali pakai, menu pengelola PPM).
 *
 * Memindahkan isi kolom lama detailform.dtform_lingkup (HTML) menjadi
 * baris-baris tabel lingkup, lalu memindahkan jawaban tilik lama
 * (auditjawabdetail) ke auditjawab yang kini berelasi ke butir lingkup.
 *
 * Halaman: /admin/migrasi
 */
class Migrasi extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/migrasi.js?v=1.0"));
        $this->load->model('LingkupModel', 'lingkup');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'migrasi/index';
        $this->data['title']   = 'Migrasi Lingkup Audit';
        $this->data['js']      = $this->load->get_js_files();
        $this->data['auditmenu'] = 'active';
        $this->data['status']  = $this->lingkup->status();
        $this->data['contoh']  = $this->contohParse();
        $this->template($this->data, $this->module);
    }

    /** Contoh hasil parse beberapa pertanyaan, untuk dilihat sebelum dijalankan. */
    private function contohParse() {
        if (!$this->lingkup->kolomLamaAda()) {
            return array();
        }

        $this->db->select('dtform_id, dtform_pertanyaan, dtform_lingkup');
        $this->db->from('detailform');
        $this->db->where('dtform_lingkup IS NOT NULL', NULL, FALSE);
        $this->db->order_by('dtform_id', 'ASC');
        $this->db->limit(5);
        $daftar = $this->db->get()->result_array();

        $hasil = array();
        foreach ($daftar as $d) {
            $butir = lingkup_parse($d['dtform_lingkup']);
            $hasil[] = array(
                'dtform_id'      => $d['dtform_id'],
                'pertanyaan'     => lingkup_bersihkan($d['dtform_pertanyaan']),
                'jml_lama'       => hitung_potong(lingkup_bersihkan($d['dtform_lingkup'])),
                'jml_tersimpan'  => $this->lingkup->hitung($d['dtform_id']),
                'butir'          => $butir,
            );
        }
        return $hasil;
    }

    /** Jalankan migrasi: siapkan struktur -> parse -> pindahkan jawaban. */
    public function jalankan() {
        $this->output->set_content_type('application/json');

        $this->lingkup->install();
        $parse   = $this->lingkup->parseSemua();
        $jawaban = $this->lingkup->migrasiJawaban();

        echo json_encode(array(
            'status'  => TRUE,
            'pesan'   => 'Migrasi selesai: ' . $parse['butir'] . ' butir lingkup dari ' . $parse['dibuat']
                       . ' pertanyaan, ' . $jawaban['dipindah'] . ' jawaban tilik dipindahkan.',
            'parse'   => $parse,
            'jawaban' => $jawaban,
            'ringkas' => $this->lingkup->status(),
        ));
    }

    /** Hapus kolom lama setelah migrasi dianggap berhasil. */
    public function hapuskolom() {
        $this->output->set_content_type('application/json');

        $status = $this->lingkup->status();
        if ($status['jml_dtform'] > 0 && $status['jml_butir'] < $status['jml_dtform']) {
            echo json_encode(array(
                'status' => FALSE,
                'pesan'  => 'Kolom lama belum boleh dihapus: baru ' . $status['jml_butir'] . ' butir untuk '
                          . $status['jml_dtform'] . ' pertanyaan. Jalankan migrasi lebih dahulu.',
            ));
            return;
        }

        if ($this->lingkup->hapusKolomLama()) {
            echo json_encode(array(
                'status' => TRUE,
                'pesan'  => 'Kolom detailform.dtform_lingkup dihapus. Struktur baru sudah dipakai sepenuhnya.',
                'ringkas' => $this->lingkup->status(),
            ));
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Kolom lama gagal dihapus.'));
        }
    }
}
