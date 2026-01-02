<?php

class Berita extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->js(base_url("assets/app/umum/berita.js?v=1.0.1"));
        $this->load->model('BeritaModel', 'beritamodel');
    }

    public function index() {
        $berita_id = $this->input->get('id');    
        $berita = $this->beritamodel->getBeritaById($berita_id);
        $result = $this->beritamodel->getAllBerita();
        $this->data['js'] = $this->load->get_js_files();
        $this->load->view('baca_berita', array('berita' => $berita, 'result' => $result));
    }

}
