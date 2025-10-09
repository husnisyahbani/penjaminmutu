<?php

class Homepage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
    }

    public function index() {
        $this->load->view('homepage');
    }

}
