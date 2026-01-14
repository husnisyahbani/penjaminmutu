<?php

class Sk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/sk.js?v=1.0.2"));
        $this->load->model('SkModel', 'skmodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'sk/index';
        $this->data['title'] = 'Surat Keputusan';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['sk'] = 'active';
        $this->data['website'] = 'active';
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
                    $data['sk_file'] =  $link;

                    $data['sk_judul']  = strtoupper($this->input->post('sk_judul'));

                    if($this->skmodel->add($data)){
                        $query = array("status" => true, "pesan" => "Berhasil");
                    }else{
                        $query = array("status" => false, "pesan" => "Gagal");
                    }

                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                }else{
                    $query = array("status" => false, "pesan" => "Silahkan Tambahkan File Sk");
                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                } 
    }

    public function edit() {
        $sk_id = $this->input->post('sk_id');
        if($sk_id){
            $data = array();
            $config['upload_path'] = 'filedata';
            $config['allowed_types'] = '*';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('upload_file')) {
                $datagambar['upload_file'] = $this->upload->data();
                $link = $datagambar['upload_file']['file_name'];
                $data['sk_file'] =  $link;
            }
            $data['sk_id'] = $sk_id;
            $data['sk_judul'] = strtoupper($this->input->post('sk_judul'));
            
            if($this->skmodel->edit($data)){
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

    public function getSk($id) {
        if(isset($id)){
            $query = $this->skmodel->getSk($id);
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
        $this->skmodel->hapus($id);
    }

    public function listsk($id = null) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->skmodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->sk_judul;
            $row[] = '<a class="btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="Detail" href="'.base_url('filedata/').$field->sk_file.'" target="_blank"><i class="icon md-download" aria-hidden="true"></i> Download</a>' ;
            $row[] = date("d-m-Y H:i:s", strtotime($field->sk_create));
            $row[] = '<button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->sk_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->sk_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->skmodel->count_all($id),
            "recordsFiltered" => $this->skmodel->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
