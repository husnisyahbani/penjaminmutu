<?php

class Login extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('UsersModel', 'users');
        $this->load->js(base_url("assets/app/umum/login.js"));
    }

    public function index() {
        $submitbutton = $this->POST('submitbutton');
        if ($submitbutton) {
            $this->data['username'] = $this->input->post('username');
            $this->data['password'] = $this->input->post('password');

            $output = $this->users->login($this->data);
            
            if ($output) {
               
                $role = $this->session->userdata('role');
                if (isset($role) && $role == 'PPM') {
                    redirect(base_url('admin'));
                }else if (isset($role) && $role == 'AUDITOR') {
                    redirect(base_url('auditor'));
                }
            } else {
                echo "gagal";
                $msg = 'Email atau Password Salah';
                $this->session->set_flashdata('pesanerror', $msg);
                redirect(base_url());
            }
        } else {
            $role = $this->session->userdata('role');
            if (isset($role) && $role == 'LPM') {
                redirect(base_url('admin'));
            }else if (isset($role) && $role == 'AUDITOR') {
                redirect(base_url('auditor'));
            } else {
                $this->data['content'] = 'login';
                $this->data['title'] = 'Login';
                $this->data['js'] = $this->load->get_js_files();
                $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
                $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
                $this->template($this->data, $this->module);
                //$this->load->view('homepage');
            }
        }
    }

}
