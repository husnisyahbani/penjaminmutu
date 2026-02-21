<?php

class Visimisi extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/visimisi.js?v=1.0.2"));
        $this->load->model('VisimisiModel', 'visimisimodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'visimisi/index';
        $this->data['title'] = 'Visi & Misi';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['visimisi'] = 'active';
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
                    $data['visimisi_file'] =  $link;

                    $data['visimisi_judul']  = strtoupper($this->input->post('visimisi_judul'));

                    if($this->visimisimodel->add($data)){
                        $query = array("status" => true, "pesan" => "Berhasil");
                    }else{
                        $query = array("status" => false, "pesan" => "Gagal");
                    }

                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                }else{
                    $query = array("status" => false, "pesan" => "Silahkan Tambahkan File Visimisi");
                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                } 
    }

    public function edit() {
        $visimisi_id = $this->input->post('visimisi_id');
        if($visimisi_id){
            $data = array();
            $config['upload_path'] = 'filedata';
            $config['allowed_types'] = '*';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('upload_file')) {
                $datagambar['upload_file'] = $this->upload->data();
                $link = $datagambar['upload_file']['file_name'];
                $data['visimisi_file'] =  $link;
            }
            $data['visimisi_id'] = $visimisi_id;
            $data['visimisi_judul'] = strtoupper($this->input->post('visimisi_judul'));
            
            if($this->visimisimodel->edit($data)){
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

    public function getVisimisi($id) {
        if(isset($id)){
            $query = $this->visimisimodel->getVisimisi($id);
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
        $this->visimisimodel->hapus($id);
    }

    public function listvisimisi($id = null) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->visimisimodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->visimisi_judul;
            $row[] = '<a class="btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="Detail" href="'.base_url('filedata/').$field->visimisi_file.'" target="_blank"><i class="icon md-download" aria-hidden="true"></i> Download</a>' ;
            $row[] = date("d-m-Y H:i:s", strtotime($field->visimisi_create));
            $row[] = '<button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->visimisi_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->visimisi_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->visimisimodel->count_all($id),
            "recordsFiltered" => $this->visimisimodel->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
