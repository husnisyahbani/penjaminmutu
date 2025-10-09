<?php

class Data extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/data.js"));
        $this->load->model('DataModel', 'datamodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'LPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'data';
        $this->data['title'] = 'Data';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['data'] = 'active';
        $this->data['dokument'] = 'active';
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    public function editdata() {
        $submitdata = $this->input->post('submitdata');
        $id = $this->input->get('id');
        if (isset($submitdata)) {
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
            $data['data_id'] = $id;
            $data['data_uraian'] = strtoupper($this->input->post('data_uraian'));
            $data['data_keterangan'] = strtoupper($this->input->post('data_keterangan'));
            $data['data_kategori'] = trim($this->input->post('data_kategori'));
            
            if($this->datamodel->edit($data)){
                $msg = 'Berhasil';
                $this->session->set_flashdata('pesanberhasil', $msg);
            }else{
                $msg = 'Gagal, Data Saksi telah ada';
                $this->session->set_flashdata('pesanerror', $msg);
            }
            redirect(base_url($this->module.'/data'));
        } else {
            $this->data['content'] = 'edit_data';
            $this->data['title'] = 'Data';
            $this->data['data'] = 'active';
            $this->data['dokument'] = 'active';
            $this->data['result'] = $this->datamodel->getData($id);
            $this->data['js'] = $this->load->get_js_files();
            $this->template($this->data, $this->module);
        }
    }

    public function tambahdata() {
        $submitdata = $this->input->post('submitdata');
        if (isset($submitdata)) {
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
            $data['data_uraian'] = strtoupper($this->input->post('data_uraian'));
            $data['data_keterangan'] = strtoupper($this->input->post('data_keterangan'));
            $data['data_kategori'] = trim($this->input->post('data_kategori'));

            if( $this->datamodel->add($data)){
                $msg = 'Berhasil';
                $this->session->set_flashdata('pesanberhasil', $msg);
            }else{
                $msg = 'Gagal, Dokument Gagal Tersimpan';
                $this->session->set_flashdata('pesanerror', $msg);
            }  

            redirect(base_url($this->module.'/data'));
        } else {
            $this->data['content'] = 'add_data';
            $this->data['title'] = 'Tambah Dokument';
            $this->data['data'] = 'active';
            $this->data['dokument'] = 'active';
            $this->data['js'] = $this->load->get_js_files();
            $this->template($this->data, $this->module);
        }
    }

    public function hapusdata() {
        $id = $this->input->post('id');
        $this->datamodel->hapus($id);
    }

    public function listdata() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->datamodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->data_keterangan;
            $row[] = $field->data_uraian;
            $row[] = "<a href='".base_url('filedata/').$field->data_file."' target='_blank'>Download</a>";
            $row[] = date("d-m-Y H:i:s", strtotime($field->data_create));
            $row[] = '<button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->data_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->data_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->datamodel->count_all(),
            "recordsFiltered" => $this->datamodel->count_filtered($post['search'], $post['order']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
