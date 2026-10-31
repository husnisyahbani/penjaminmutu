<?php

class Pengendalian extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('DataModel', 'datamodel');
        $this->load->model('HomeModel', 'home');
    }

    public function index() {
        $pengendalian = $this->datamodel->getAllPengendalian();
        //var_dump($pengendalian);
        $this->load->view('dokumentku', array('dataku' => $pengendalian, 'judul' => 'Pengendalian', 'home' => $this->home));
    }

}
