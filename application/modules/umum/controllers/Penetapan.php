<?php

class Penetapan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('DataModel', 'datamodel');
    }

    public function index() {
        $penetapan = $this->datamodel->getAllPenetapan();
        $this->load->view('dokument', array('data' => $penetapan));
    }

}
