<?php

class Homepage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('UsersModel', 'users');
        $this->load->js(base_url("assets/app/umum/login.js"));
    }

    public function index() {    
                // $this->data['content'] = 'homepage';
                // $this->data['title'] = 'homepage';
                // $this->data['js'] = $this->load->get_js_files();
                // $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
                // $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
                // $this->template($this->data, $this->module);
                $this->load->view('homepage');
    }

}
