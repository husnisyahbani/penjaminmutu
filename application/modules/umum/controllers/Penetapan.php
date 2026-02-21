<?php

class Penetapan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('DataModel', 'datamodel');
    }

    public function index() {
        $penetapan = $this->datamodel->getAllPenetapan();
        //var_dump($penetapan);
        $this->load->view('dokumentku', array('dataku' => $penetapan));
    }

}
