<?php

class Ptk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditee';
        $this->load->js(base_url("assets/app/auditee/ptk.js?v=2.0"));
        $this->load->model('PtkModel', 'ptkmodel');

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
        $data['jwb_koreksi'] = $this->input->post('ptk_koreksi');
        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $data['jwb_koreksi'] = strip_tags((string) $data['jwb_koreksi'], $allowed_tags);
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
            $row[] = $field->form_nama;
            $row[] = html_escape($field->lingkup_isi);
            $row[] = $field->jwb_hasil;
            $row[] = $field->jwb_temuan
                ? '<span class="badge badge-warning">' . html_escape($field->jwb_temuan) . '</span>' : '';
            $row[] = $field->jwb_catatan;

            $tombol = '<button type="button" class="edit btn btn-sm btn-icon btn-primary"'
                . ' data-info="Tulis rencana koreksi butir ini"'
                . ' dtform_id="' . $field->dtform_id . '"'
                . ' audit_id="' . $field->audit_id . '"'
                . ' lingkup_id="' . $field->lingkup_id . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button>';
            $koreksi = isset($field->jwb_koreksi) ? (string) $field->jwb_koreksi : '';
            $row[] = '<div class="ptk-koreksi">' . $koreksi . '</div><div class="tabel-aksi">' . $tombol . '</div>';
  
            
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
