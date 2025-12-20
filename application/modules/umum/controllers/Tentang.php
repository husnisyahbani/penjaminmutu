<?php

class Homepage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
    }

    public function index() { 
        $id = $this->input->get('id');   
        $this->load->view('strukturorganisasi', ['id' => $id]);
    }

}
