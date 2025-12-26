<?php

class Berita extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/berita.js?v=1.0.1"));
        $this->load->model('BeritaModel', 'beritamodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $berita_id = $this->input->get('id');    
        $berita = $this->beritamodel->getBeritaById($berita_id);
        $result = $this->beritamodel->getAllBerita();
        $this->data['js'] = $this->load->get_js_files();
        $this->load->view('baca_berita', array('berita' => $berita, 'result' => $result));
    }

}
