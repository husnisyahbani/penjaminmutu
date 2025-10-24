<?php

class Akun extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/akun.js"));
        $this->load->model('AkunModel', 'akun');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['akun'] = 'active';
        $this->data['content'] = 'akun';
        $this->data['title'] = 'Akun';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    public function password() {
        $submitpass = $this->input->post('submitpass');
        if (isset($submitpass)) {
           $data = array();
           $passlama = md5($this->input->post('passlama'));
           
           $data['users_id'] = $this->session->userdata('users_id');
           $data['username'] = $this->session->userdata('username');
           $data['password'] = md5($this->input->post('password'));
           if($this->akun->passlama($passlama)&&$this->akun->edit($data)){
               $msg = 'Berhasil';
               $this->session->set_flashdata('pesanberhasil', $msg);
           }else{
               $msg = 'Gagal, password lama salah';
               $this->session->set_flashdata('pesanerror', $msg);
           }
           redirect(base_url($this->module));
       } else {
           $this->data['content'] = 'password';
           $this->data['title'] = 'Ubah Password';
           $this->data['js'] = $this->load->get_js_files();
           $this->template($this->data, $this->module);
       }
   }

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    public function hapus() {
        $id = $this->input->post('id');
        $this->akun->hapus($id);
    }
    
    public function reset() {
        $id = $this->input->post('id');
        $this->akun->reset($id);
    }

    public function edit() {
        $submitakun = $this->input->post('submitakun');
        $id = $this->input->get('id');
        if (isset($submitakun)) {
            $data = array();
            $data['users_id'] = $id;
            $data['nama'] = $this->input->post('nama');
            $data['unitkerja'] = $this->input->post('unitkerja');
            $data['username'] = $this->input->post('username');
            $data['role'] = $this->input->post('role');

            if($this->akun->edit($data)){
                $msg = 'Berhasil';
                $this->session->set_flashdata('pesanberhasil', $msg);
            }else{
                $msg = 'Gagal, Data Akun Telah Ada';
                $this->session->set_flashdata('pesanerror', $msg);
            }
           redirect(base_url($this->module.'/akun'));
        } else {
            $this->data['content'] = 'edit_akun';
            $this->data['title'] = 'Akun';
            $this->data['akun'] = 'active';
            $this->data['result'] = $this->akun->getAkunById($id);
            $this->data['js'] = $this->load->get_js_files();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->data['result'] = $this->akun->getAkunById($id);
            $this->template($this->data, $this->module);
        }
    }

    public function tambah() {
        $submitakun = $this->input->post('submitakun');
        if (isset($submitakun)) {
            $data = array();
            $data['nama'] = $this->input->post('nama');
            $data['unitkerja'] = $this->input->post('unitkerja');
            $data['username'] = $this->input->post('username');
            $data['password'] = md5($this->input->post('password'));
            $data['role'] = $this->input->post('role');
            if(!$this->akun->is_exist($data) && $this->akun->add($data)){
                $msg = 'Berhasil';
                $this->session->set_flashdata('pesanberhasil', $msg);
            }else{
                $msg = 'Gagal, Data Username telah ada';
                $this->session->set_flashdata('pesanerror', $msg);
            }
            redirect(base_url($this->module.'/akun'));
        } else {
            $this->data['content'] = 'add_akun';
            $this->data['title'] = 'Akun';
            $this->data['akun'] = 'active';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module);
        }
    }

    public function listakun() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->akun->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->nama;
            $row[] = $field->unitkerja;
            $row[] = $field->username;
            $row[] = $field->password;
            $row[] = $field->role;

            $row[] = '<button class="reset btn btn-sm btn-icon btn-pure btn-default on-default reset-row"
                      data-toggle="tooltip" data-original-title="Reset" id=' . $field->users_id . '><i class="icon md-refresh" aria-hidden="true"></i></button><button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
                      data-toggle="tooltip" data-original-title="Edit" id=' . $field->users_id . '><i class="icon md-edit" aria-hidden="true"></i></button>
                    <button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->users_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->akun->count_all(),
            "recordsFiltered" => $this->akun->count_filtered($post['search'], $post['order']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
    
    public function kota($param = 0) {
        $this->db->select('id');
        $this->db->select('name');
        $this->db->from('regencies');
        $this->db->where('province_id', $param);
        $query = $this->db->get();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query->result());
    }

}
