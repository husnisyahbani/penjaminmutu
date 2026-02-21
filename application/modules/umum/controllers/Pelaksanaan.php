<?php

class Pelaksanaan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('DataModel', 'datamodel');
    }

    public function index() {
        $pelaksanaan = $this->datamodel->getAllPelaksanaan();
        //var_dump($pelaksanaan);
        $this->load->view('dokumentku', array('dataku' => $pelaksanaan, 'judul' => 'Pelaksanaan'));
    }

}
