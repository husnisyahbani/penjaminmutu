<?php

class Evaluasi extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('DataModel', 'datamodel');
    }

    public function index() {
        $evaluasi = $this->datamodel->getAllEvaluasi();
        //var_dump($evaluasi);
        $this->load->view('dokumentku', array('dataku' => $evaluasi, 'judul' => 'Evaluasi'));
    }

}
