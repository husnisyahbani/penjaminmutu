<?php

class Peningkatan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/peningkatan.js?v=1.0"));
        $this->load->model('DataModel', 'datamodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITOR') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'data';
        $this->data['title'] = 'Dokumen Mutu';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['data'] = 'active';
        $this->data['peningkatan'] = 'active';
        $this->data['dokument'] = 'active';
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    public function tambah() {
                $data = array();
                $config['upload_path'] = 'filedata';
                $config['allowed_types'] = '*';
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                
                if ($this->upload->do_upload('upload_file')) {
                    $datagambar['upload_file'] = $this->upload->data();
                    $link = $datagambar['upload_file']['file_name'];
                    $data['data_file'] =  $link;

                    $data['data_uraian']  = strtoupper($this->input->post('data_uraian'));
                    $data['data_keterangan'] = strtoupper($this->input->post('data_keterangan'));
                    $data['data_kategori'] = trim($this->input->post('data_kategori'));

                    if($this->datamodel->add($data)){
                        $query = array("status" => true, "pesan" => "Berhasil");
                    }else{
                        $query = array("status" => false, "pesan" => "Gagal");
                    }

                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                }else{
                    $query = array("status" => false, "pesan" => "Silahkan Tambahkan File Data");
                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                } 
    }

    public function edit() {
        $data_id = $this->input->post('data_id');
        if($data_id){
            $data = array();
            $config['upload_path'] = 'filedata';
            $config['allowed_types'] = '*';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('upload_file')) {
                $datagambar['upload_file'] = $this->upload->data();
                $link = $datagambar['upload_file']['file_name'];
                $data['data_file'] =  $link;
            }
            $data['data_id'] = $data_id;
            $data['data_uraian'] = strtoupper($this->input->post('data_uraian'));
            $data['data_keterangan'] = strtoupper($this->input->post('data_keterangan'));
            $data['data_kategori'] = trim($this->input->post('data_kategori'));
            
            if($this->datamodel->edit($data)){
                $query = array("status" => true, "pesan" => "Berhasil");
            }else{
                $query = array("status" => false, "pesan" => "Gagal");
            }

            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);

        }else{
            $query = array("status" => false, "pesan" => "Akses Ditolak");
                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
        } 
    }

    public function getData($id) {
        if(isset($id)){
            $query = $this->datamodel->getData($id);
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
        $this->datamodel->hapus($id);
    }

    public function listdata($id = "PENINGKATAN") {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->datamodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->data_uraian;
            $row[] = $field->data_keterangan;
            $row[] = '<a class="btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="Detail" href="'.base_url('filedata/').$field->data_file.'" target="_blank"><i class="icon md-download" aria-hidden="true"></i> Download</a>' ;
            $row[] = $field->data_kategori;
            $row[] = date("d-m-Y H:i:s", strtotime($field->data_create));
            $row[] = '<button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->data_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->data_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->datamodel->count_all($id),
            "recordsFiltered" => $this->datamodel->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
