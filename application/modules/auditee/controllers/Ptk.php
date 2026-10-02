<?php

class Ptk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditee';
        $this->load->js(base_url("assets/app/auditee/ptk.js?v=3.0"));
        // Informasi tombol aksi saat hover (lihat assets/app/tabel-aksi.css)
        $this->load->js(base_url("assets/app/tabel-aksi.js?v=1.0"));
        $this->load->model('PtkModel', 'ptkmodel');
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
            $this->data['observasi'] = $this->ptkmodel->totalObservasi();
            $this->data['minor'] = $this->ptkmodel->totalMinor();
            $this->data['mayor'] = $this->ptkmodel->totalMayor();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
    }

   

    public function koreksi() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['lingkup_id'] = $this->input->post('lingkup_id');
        $data['jwb_koreksi'] = $this->_teksBaris($this->input->post('ptk_koreksi'));
        $status = $this->ptkmodel->koreksi($data);
        
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
    public function getbutir($audit_id = NULL, $lingkup_id = NULL) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $row = $this->ptkmodel->getButir($audit_id, $lingkup_id);
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

    /**
     * Teks polos yang tetap mempertahankan baris baru (rencana koreksi).
     *
     * Rencana koreksi lama tersimpan sebagai HTML (versi editor); tag blok
     * diubah menjadi baris baru supaya tampil rapi di textarea dan tabel.
     */
    private function _teksBaris($nilai) {
        $teks = (string) $nilai;

        if (strpos($teks, '<') !== FALSE) {
            $teks = preg_replace('/<br\s*\/?>/i', "\n", $teks);
            $teks = preg_replace('/<li\b[^>]*>/i', '- ', $teks);
            $teks = preg_replace('/<\/(p|div|li|ul|ol|h[1-6]|tr)>/i', "\n", $teks);
            $teks = strip_tags($teks);
        }

        $teks = html_entity_decode($teks, ENT_QUOTES, 'UTF-8');
        $teks = str_replace(array("\r\n", "\r"), "\n", $teks);
        $teks = preg_replace('/[ \t]+/', ' ', $teks);
        $teks = preg_replace('/ ?\n ?/', "\n", $teks);
        $teks = preg_replace('/\n{3,}/', "\n\n", $teks);

        return trim($teks);
    }

    public function listptk() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->ptkmodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->form_nama)) . '</div>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->lingkup_isi)) . '</div>';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->jwb_hasil)) . '</div>';
            $row[] = $field->jwb_temuan
                ? '<span class="badge badge-warning">' . html_escape($field->jwb_temuan) . '</span>' : '';
            $row[] = '<div class="ptk-klamp">' . html_escape($this->_teks($field->jwb_catatan)) . '</div>';

            $tombol = '<button type="button" class="edit btn btn-sm btn-icon btn-primary"'
                . ' data-info="Tulis rencana koreksi butir ini"'
                . ' dtform_id="' . $field->dtform_id . '"'
                . ' audit_id="' . $field->audit_id . '"'
                . ' lingkup_id="' . $field->lingkup_id . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button>';
            $koreksi = $this->_teksBaris(isset($field->jwb_koreksi) ? $field->jwb_koreksi : '');
            $isi = $koreksi === ''
                ? '<span class="ptk-koreksi-kosong">Belum ada rencana koreksi</span>'
                : '<div class="ptk-klamp ptk-koreksi">' . nl2br(html_escape($koreksi)) . '</div>';
            $row[] = $isi . '<div class="tabel-aksi ptk-koreksi-aksi">' . $tombol . '</div>';
  
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->ptkmodel->count_all(),
            "recordsFiltered" => $this->ptkmodel->count_filtered($post['search'], $post['order']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
