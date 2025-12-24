<?php

class Berita extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/berita.js?v=1.0.0"));
        $this->load->model('BeritaModel', 'beritamodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'berita/index';
        $this->data['title'] = 'Berita';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['berita'] = 'active';
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
                    $data['berita_file'] =  $link;

                    $data['berita_judul']  = $this->input->post('berita_judul');
                    $data['berita_deskripsi'] = $this->input->post('berita_deskripsi');
                    $data['berita_isi'] = trim($this->input->post('berita_isi'));

                    if($this->beritamodel->add($data)){
                        $query = array("status" => true, "pesan" => "Berhasil");
                    }else{
                        $query = array("status" => false, "pesan" => "Gagal");
                    }

                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                }else{
                    $query = array("status" => false, "pesan" => "Silahkan Tambahkan File Berita");
                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                } 
    }

    public function edit($berita_id = null) {
        if($berita_id){
            $data = array();
            $config['upload_path'] = 'filedata';
            $config['allowed_types'] = '*';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('upload_file')) {
                $datagambar['upload_file'] = $this->upload->data();
                $link = $datagambar['upload_file']['file_name'];
                $data['berita_file'] =  $link;
            }
            $data['berita_id'] = $berita_id;
            $data['berita_judul']  = $this->input->post('berita_judul');
            $data['berita_deskripsi'] = $this->input->post('berita_deskripsi');
            $data['berita_isi'] = trim($this->input->post('berita_isi'));
            
            if($this->beritamodel->edit($data)){
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

    public function getBerita($id) {
        if(isset($id)){
            $query = $this->beritamodel->getBerita($id);
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
        $this->beritamodel->hapus($id);
    }

    public function listdata($id = null) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->beritamodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->berita_judul;
            $row[] = $field->berita_deskripsi;
            $row[] = '<a class="btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="Detail" href="'.base_url('filedata/').$field->berita_file.'" target="_blank"><i class="icon md-download" aria-hidden="true"></i> Download</a>' ;
            $row[] = $field->berita_isi;
            $row[] = date("d-m-Y H:i:s", strtotime($field->berita_create));
            $row[] = '<button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->berita_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->berita_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->beritamodel->count_all($id),
            "recordsFiltered" => $this->beritamodel->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
