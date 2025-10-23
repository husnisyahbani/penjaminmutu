<?php

class Formaudit extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/formaudit.js?v=1.18"));
        $this->load->model('FormulirModel', 'formulir');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'formaudit/index';
        $this->data['title'] = 'Formulir Audit';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['auditmenu'] = 'active';
        $this->data['formaudit'] = 'active';
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

   public function tambah() {
        $form_nama = $this->input->post('form_nama');
        if($form_nama){
                $data = array();
                $data['form_nama']  = strtoupper($form_nama);
                $data['form_kode'] = $this->input->post('form_kode');
                $data['form_deskripsi']   = $this->input->post('form_deskripsi');

                if ($this->formulir->add($data)) {
                    $query = array("status" => true, "pesan" => "Berhasil");
                } else {
                    $query = array("status" => false, "pesan" => "Gagal");
                }

                header('Access-Control-Allow-Origin: *');
                header('Content-Type: application/json');
                echo json_encode($query);

        }else{
            $query = array("status" => false, "pesan" => "Silahkan mengisi form audit");
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        } 
    }

    public function edit() {
        $form_id = $this->input->post('form_id');
        if($form_id){
                $data = array();
                $data['form_nama']  = strtoupper($this->input->post('form_nama'));
                $data['form_kode'] = $this->input->post('form_kode');
                $data['form_deskripsi']   = $this->input->post('form_deskripsi');
                $data['form_id'] = $form_id;

                if ($this->formulir->edit($data)) {
                    $query = array("status" => true, "pesan" => "Berhasil");
                } else {
                    $query = array("status" => false, "pesan" => "Gagal");
                }

                header('Access-Control-Allow-Origin: *');
                header('Content-Type: application/json');
                echo json_encode($query);

        }else{
            $query = array("status" => false, "pesan" => "Silahkan mengisi form audit");
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        } 
    }

    public function getFormulirById($id) {
        if(isset($id)){
            $query = $this->formulir->getFormulir($id);
            $query['status'] = true;
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        }else{
            $query = array("status"=>false);
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query); 
        }
    }

    public function hapus() {
        $id = $this->input->post('id');
        $this->formulir->hapus($id);
    }

    public function listformulir() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->formulir->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->form_nama;
            $row[] = $field->form_kode;
            $row[] = $field->form_deskripsi;
            $row[] = date("d-m-Y H:i:s", strtotime($field->form_create));
            $row[] = '<button class="detail btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Detail" id=' . $field->form_id . '><i class="icon md-book" aria-hidden="true"></i></button><button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->form_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->form_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->formulir->count_all(),
            "recordsFiltered" => $this->formulir->count_filtered($post['search'], $post['order']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
