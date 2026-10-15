<?php

class Ptk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditee';
        $this->load->js(base_url("assets/app/auditee/ptk.js?v=3.2"));
        // Informasi tombol aksi saat hover (lihat assets/app/tabel-aksi.css)
        $this->load->js(base_url("assets/app/tabel-aksi.js?v=1.0"));
        $this->load->model('PtkModel', 'ptkmodel');
        $this->load->model('PeriodeModel', 'periode');
        // Pembantu teks (lingkup_bersihkan) untuk merapikan isi kolom tabel.
        $this->load->helper('lingkup');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITEE') {
            redirect(base_url());
        }
    }

    public function index() {
            $this->data['content'] = 'ptk/index';
            $this->data['title'] = 'PERMINTAAN TINDAKAN KOREKSI';
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

            /* Angka kartu statistik: satu kueri untuk total, per kategori,
               dan yang belum punya rencana koreksi. */
            $ringkasan = $this->ptkmodel->ringkasan($filter);
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
     * ikut berubah saat saringan periode diganti (lihat assets/app/auditee/ptk.js).
     */
    public function statistik() {
        $this->output->set_content_type('application/json');

        $periode_id = $this->input->post('periode_id');
        $ringkasan  = $this->ptkmodel->ringkasan($periode_id);

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


    public function koreksi() {
        $data = array();
        $data['audit_id']    = (int) $this->input->post('audit_id');
        $data['dtjwb_id']    = (int) $this->input->post('dtjwb_id');
        $data['jwb_koreksi'] = $this->_teksBaris($this->input->post('ptk_koreksi'));

        /* Penjagaan: butir harus milik auditee ini (getButir menyaringnya) dan
           auditnya sudah berstatus SELESAI. */
        $butir = $this->ptkmodel->getButir($data['audit_id'], $data['dtjwb_id']);
        $status = false;
        if (!empty($butir) && strtoupper(trim((string) $butir['audit_status'])) === 'SELESAI') {
            $data['jwb_id'] = (int) $butir['jwb_id'];
            $status = $this->ptkmodel->koreksi($data);
        }
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function hapus() {
        $id = $this->input->post('id');
        $this->ptkmodel->hapus($id);
    }

    /** Data satu butir temuan untuk modal rencana koreksi. */
    public function getbutir($audit_id = NULL, $dtjwb_id = NULL) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $row = $this->ptkmodel->getButir($audit_id, $dtjwb_id);
        if (!$row) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Data tidak ditemukan.'));
            return;
        }
        $row['status'] = TRUE;
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

        $list = $this->ptkmodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order'], $periode_id);
        $data = array();
        $no = $this->input->post('start');
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
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->lingkup_isi)) . '</div>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->jwb_hasil)) . '</div>';
            $row[] = $temuan === ''
                ? ''
                : '<span class="badge ' . $warna . '">' . html_escape($temuan) . '</span>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->jwb_catatan)) . '</div>';

            $koreksi = $this->_teksBaris(isset($field->jwb_koreksi) ? $field->jwb_koreksi : '');
            $isi = $koreksi === ''
                ? '<span class="ptk-koreksi-kosong">Belum ada rencana koreksi</span>'
                : '<div class="ptk-klamp ptk-koreksi">' . nl2br(html_escape($koreksi)) . '</div>';

            if ($temuan === 'S') {
                /* Butir "S" (sesuai) tidak perlu rencana koreksi - pada halaman
                   PTK baris ini memang tidak ditampilkan, penjagaan ini hanya
                   untuk keamanan. */
                $row[] = '<span class="text-muted">Tidak perlu koreksi</span>';
            } else {
                $tombol = '<button type="button" class="edit btn btn-sm btn-icon btn-primary"'
                    . ' data-info="Tulis rencana koreksi butir ini"'
                    . ' dtjwb_id="' . (int) $field->dtjwb_id . '"'
                    . ' audit_id="' . (int) $field->audit_id . '">'
                    . '<i class="icon md-edit" aria-hidden="true"></i></button>';

                /* Rencana koreksi hanya dapat ditulis setelah audit selesai. */
                if (strtoupper(trim((string) $field->audit_status)) === 'SELESAI') {
                    $aksi = '<div class="tabel-aksi ptk-koreksi-aksi">' . $tombol . '</div>';
                } else {
                    $aksi = '<div class="ptk-koreksi-ket">Dapat diisi setelah audit selesai</div>';
                }
                $row[] = $isi . $aksi;
            }

            /* Kolom Status (diisi auditor) */
            $status_ptk = isset($field->jwb_status) ? strtoupper(trim((string) $field->jwb_status)) : '';
            if ($status_ptk === 'CLOSED') {
                $status_badge = '<span class="badge badge-success">CLOSED</span>';
            } elseif ($status_ptk === 'PROSES') {
                $status_badge = '<span class="badge badge-warning">PROSES</span>';
            } elseif ($status_ptk === 'OPEN') {
                $status_badge = '<span class="badge badge-danger">OPEN</span>';
            } else {
                $status_badge = '<span class="badge badge-default">BELUM DIISI</span>';
            }
            $row[] = '<div class="text-center">' . $status_badge . '</div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->ptkmodel->count_all($periode_id),
            "recordsFiltered" => $this->ptkmodel->count_filtered($post['search'], $post['order'], $periode_id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
