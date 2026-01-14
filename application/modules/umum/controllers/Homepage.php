<?php

class Homepage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('BeritaModel', 'beritamodel');
        $this->load->model('SkModel', 'skmodel');
        $this->load->model('PengumumanModel', 'pengumumanmodel');
        $this->load->js(base_url("assets/app/umum/login.js"));
    }

    public function index() {    
                $berita = $this->beritamodel->getAllBerita();
                $pengumuman = $this->pengumumanmodel->getAllPengumuman();
                $sk = $this->skmodel->getAllSk();
                $this->load->view('homepage', array('berita' => $berita, 'pengumuman' => $pengumuman, 'sk' => $sk));
    }

}
