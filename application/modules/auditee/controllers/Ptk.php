<?php

class Ptk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditee';
        $this->load->js(base_url("assets/app/auditor/ptk.js?v=1.0"));
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
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
    }

   

    public function koreksi() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_koreksi'] = $this->input->post('ptk_koreksi');
        $status = $this->ptk->koreksi($data);
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function hapus() {
        $id = $this->input->post('id');
        $this->mutu->hapus($id);
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
            $row[] = $field->dtform_lingkup;
            $row[] = $field->jwb_hasil;
            $row[] = $field->jwb_temuan;
            $row[] = $field->jwb_catatan;
            $row[] = $field->jwb_koreksi.' <button type="button" class="edit btn btn-warning btn-xs waves-effect waves-classic" dtform_id=' . $field->dtform_id.' audit_id=' . $field->audit_id.'><i class="icon md-edit" aria-hidden="true"></i></button>';;
  
            
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
