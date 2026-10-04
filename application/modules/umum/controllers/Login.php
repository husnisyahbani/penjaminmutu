<?php

class Login extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('UsersModel', 'users');
        // pengaturan tema halaman depan (warna, logo, teks) untuk tampilan login
        $this->load->model('HomeModel', 'home');
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
                }else if (isset($role) && $role == 'AUDITEE') {
                    redirect(base_url('auditee'));
                }
            } else {
                $msg = 'Username atau Password Salah';
                $this->session->set_flashdata('pesanerror', $msg);
                redirect(base_url('login'));
            }
        } else {
            $role = $this->session->userdata('role');
            if (isset($role) && $role == 'PPM') {
                redirect(base_url('admin'));
            }else if (isset($role) && $role == 'AUDITOR') {
                redirect(base_url('auditor'));
            }else if (isset($role) && $role == 'AUDITEE') {
                redirect(base_url('auditee'));
            } else {
                $this->data['title'] = 'Login';
                $this->data['home'] = $this->home;
                $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
                $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
                // halaman login memakai shell tema halaman depan
                // (umum/includes/hp_top & hp_bottom), bukan layout lama.
                $this->load->view('login', $this->data);
            }
        }
    }

}
