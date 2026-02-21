<?php

class Peningkatan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('DataModel', 'datamodel');
    }

    public function index() {
        $peningkatan = $this->datamodel->getAllPeningkatan();
        //var_dump($penetapan);
        $this->load->view('dokumentku', array('dataku' => $peningkatan, 'judul' => 'Peningkatan'));
    }

}
