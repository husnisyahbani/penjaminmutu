<?php

/**
 * PTK (Permintaan Tindakan Koreksi) - halaman auditor.
 *
 * Menampilkan temuan (OB / TS MINOR / TS MAYOR) yang berasal dari daftar
 * tilik yang DIBUAT AUDITOR YANG SEDANG MASUK, lengkap dengan rencana
 * koreksi yang ditulis auditee. Halaman ini hanya untuk melihat: rencana
 * koreksi tetap diisi oleh auditee pada halaman PTK miliknya.
 *
 * Data mengikuti periode yang dipilih; bawaannya periode aktif.
 */
class Ptk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/ptk.js?v=2.2"));
        // Informasi tombol aksi saat hover (lihat assets/app/tabel-aksi.css)
        $this->load->js(base_url("assets/app/tabel-aksi.js?v=1.0"));
        $this->load->model('PtkdataModel', 'ptkdata');
        $this->load->model('PeriodeModel', 'periode');
        // Pembantu teks (lingkup_bersihkan) untuk merapikan isi kolom tabel.
        $this->load->helper('lingkup');

        /* Baris yang tampil = tilik pada audit yang ditangani auditor ini. */
        $this->ptkdata->aturPemilik('auditor');
        /* Susunan kolom tabel halaman ini (No, Formulir, Auditee, Unit,
           Butir, Hasil, Temuan, Catatan, Rencana Koreksi, Status); null = tidak
           dapat diurutkan. */
        $this->ptkdata->aturKolom(array(null, 'f.form_nama', 'au.auditee', 'au.unit',
            'dj.dtjwb_pertanyaan', 'dj.dtjwb_hasil', 'dj.dtjwb_temuan', 'dj.dtjwb_catatan', null, 'dj.dtjwb_status'));
        $this->ptkdata->aturCari(array('f.form_nama', 'au.auditee', 'au.unit',
            'dt.dtform_pertanyaan', 'dj.dtjwb_pertanyaan', 'dj.dtjwb_hasil',
            'dj.dtjwb_temuan', 'dj.dtjwb_catatan', 'dj.dtjwb_status'));

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITOR') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'ptk/index';
        $this->data['title'] = 'MONITORING PTK';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['ptk'] = 'active';
        /* Filter periode: bawaan = periode aktif. Bila tidak ada periode
           aktif, seluruh temuan ditampilkan. Tabel dan kartu statistik
           mengikuti periode yang dipilih.
           Bila tabel periode belum disiapkan (kolom mutu_audit.periode_id
           belum ada), kotak saring tidak ditampilkan. */
        $this->data['list_periode'] = $this->periode->siap() ? $this->periode->getAll() : array();
        $this->data['periode_aktif'] = $this->periode->aktifId();
        $filter = $this->data['periode_aktif'];

        /* Angka kartu statistik: satu kueri untuk total, per kategori, dan
           yang belum punya rencana koreksi. */
        $ringkasan = $this->ptkdata->ringkasan($filter);
        $this->data['observasi'] = $ringkasan['observasi'];
        $this->data['minor'] = $ringkasan['minor'];
        $this->data['mayor'] = $ringkasan['mayor'];
        $this->data['total_temuan'] = $ringkasan['total'];
        $this->data['tanpa_koreksi'] = $ringkasan['tanpa_koreksi'];
        $this->data['sudah_koreksi'] = $ringkasan['total'] - $ringkasan['tanpa_koreksi'];
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    /**
     * Ringkasan temuan untuk periode terpilih - dipakai kartu statistik agar
     * ikut berubah saat saringan periode diganti (lihat assets/app/auditor/ptk.js).
     */
    public function statistik() {
        $this->output->set_content_type('application/json');

        $periode_id = $this->input->post('periode_id');
        $ringkasan  = $this->ptkdata->ringkasan($periode_id);

        echo json_encode(array(
            'status'        => TRUE,
            'total'         => (int) $ringkasan['total'],
            'observasi'     => (int) $ringkasan['observasi'],
            'minor'         => (int) $ringkasan['minor'],
            'mayor'         => (int) $ringkasan['mayor'],
            'tanpa_koreksi' => (int) $ringkasan['tanpa_koreksi'],
            'sudah_koreksi' => (int) $ringkasan['total'] - (int) $ringkasan['tanpa_koreksi'],
        ));
    }

    /** Sumber data DataTables halaman PTK. */
    public function listptk() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');

        /* Saringan periode (bawaan periode aktif dikirim ptk.js;
           kosong = semua periode). */
        $periode_id = $this->input->post('periode_id');

        $list = $this->ptkdata->get_datatables($post['length'], $post['start'], $post['search'], $post['order'], $periode_id);
        $data = array();
        $no = $this->input->post('start');
        /* Kolom status tersedia setelah database/status_ptk.sql diimpor; bila
           belum, model dapat menyiapkannya sendiri (PtkdataModel). */
        $status_siap = $this->ptkdata->statusSiap() || $this->ptkdata->installStatus();
        foreach ($list as $field) {
            $no++;
            $temuan = strtoupper(trim((string) $field->jwb_temuan));

            /* Warna badge mengikuti jenis penilaian (sama dengan halaman delik). */
            $warna = 'badge-warning';
            if ($temuan === 'S') {
                $warna = 'badge-success';
            } elseif ($temuan === 'OB') {
                $warna = 'badge-info';
            } elseif ($temuan === 'TS MINOR') {
                $warna = 'badge-warning';
            } elseif ($temuan === 'TS MAYOR') {
                $warna = 'badge-danger';
            }

            $row = array();
            $row[] = $no;
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->form_nama)) . '</div>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->auditee)) . '</div>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->unit)) . '</div>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->lingkup_isi)) . '</div>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->jwb_hasil)) . '</div>';
            $row[] = $temuan === ''
                ? ''
                : '<span class="badge ' . $warna . '">' . html_escape($temuan) . '</span>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->jwb_catatan)) . '</div>';

            /* Rencana koreksi hanya ditampilkan (diisi auditee). */
            $koreksi = $this->_teksBaris(isset($field->jwb_koreksi) ? $field->jwb_koreksi : '');
            $row[] = $koreksi === ''
                ? '<span class="ptk-koreksi-kosong">Belum ada rencana koreksi</span>'
                : '<div class="ptk-klamp ptk-koreksi">' . nl2br(html_escape($koreksi)) . '</div>';

            /* ---- kolom Status: TEKS BEBAS yang diketik auditor ----
               Dipakai mencatat tindak lanjut tiap temuan, dengan cara isi
               yang sama seperti auditee menulis rencana koreksi: tombol
               pensil -> modal berisi textarea -> simpan. */
            if (!$status_siap) {
                $row[] = '<span class="ptk-status-notsiap"'
                    . ' title="Kolom status belum tersedia di basis data">-</span>';
            } else {
                $status_ptk = $this->_teksBaris(isset($field->jwb_status) ? $field->jwb_status : '');
                $tombol_status = '<button type="button" class="edit-status btn btn-sm btn-icon btn-primary"'
                    . ' data-info="Tulis status tindak lanjut" aria-label="Tulis status"'
                    . ' dtjwb_id="' . (int) $field->dtjwb_id . '"'
                    . ' audit_id="' . (int) $field->audit_id . '">'
                    . '<i class="icon md-edit" aria-hidden="true"></i></button>';

                $row[] = '<div class="ptk-koreksi-sel">'
                    . '<div class="tabel-aksi ptk-koreksi-aksi">' . $tombol_status . '</div>'
                    . ($status_ptk === ''
                        ? '<span class="ptk-koreksi-kosong">Belum ada status</span>'
                        : '<div class="ptk-klamp ptk-status-teks">' . nl2br(html_escape($status_ptk)) . '</div>')
                    . '</div>';
            }

            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->ptkdata->count_all($periode_id),
            "recordsFiltered" => $this->ptkdata->count_filtered($post['search'], $post['order'], $periode_id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    /**
     * Perbarui status temuan PTK oleh auditor (OPEN / PROSES / CLOSED).
     */
    /**
     * Simpan status tindak lanjut PTK.
     *
     * Status sekarang TEKS BEBAS (bukan lagi pilihan OPEN / PROSES /
     * CLOSED). Nama parameter lama ("status") tetap diterima supaya
     * pemanggil lama tidak rusak.
     */
    public function updatestatus() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $dtjwb_id = (int) $this->input->post('dtjwb_id');
        $audit_id = (int) $this->input->post('audit_id');

        $teks = $this->input->post('ptk_status');
        if ($teks === NULL) {
            $teks = $this->input->post('status');
        }
        $teks = $this->_teksBaris($teks);

        if ($dtjwb_id <= 0) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'ID temuan tidak valid.'));
            return;
        }

        /* Batasi panjang teks supaya tidak membebani basis data. */
        if (strlen($teks) > 2000) {
            $teks = substr($teks, 0, 2000);
        }

        $hasil = $this->ptkdata->updateStatus($dtjwb_id, $teks, $audit_id);

        echo json_encode(array(
            'status' => (bool) $hasil,
            'pesan'  => $hasil
                ? 'Status tersimpan.'
                : 'Status gagal disimpan. Pastikan temuan ini termasuk audit Anda.'
        ));
    }

    /** Data satu butir temuan untuk modal status tindak lanjut. */
    public function getbutir($audit_id = NULL, $dtjwb_id = NULL) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $row = $this->ptkdata->getButir($audit_id, $dtjwb_id);
        if (!$row) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Data tidak ditemukan.'));
            return;
        }

        $row['status'] = TRUE;
        /* Teks dibersihkan supaya aman dan enak dibaca pada modal. */
        $row['lingkup_isi'] = $this->_teks(isset($row['lingkup_isi']) ? $row['lingkup_isi'] : '');
        $row['jwb_koreksi'] = $this->_teksBaris(isset($row['jwb_koreksi']) ? $row['jwb_koreksi'] : '');
        $row['jwb_status']  = $this->_teksBaris(isset($row['jwb_status']) ? $row['jwb_status'] : '');

        echo json_encode($row);
    }

    /** Teks polos satu baris (untuk kolom tabel). */
    private function _teks($nilai) {
        return lingkup_bersihkan($nilai);
    }

    /** Teks polos rencana koreksi (helper bersama, lihat lingkup_helper). */
    private function _teksBaris($nilai) {
        return lingkup_teks_baris($nilai);
    }

}

