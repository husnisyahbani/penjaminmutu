<?php

class Homepage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('BeritaModel', 'beritamodel');
        $this->load->model('SkModel', 'skmodel');
        $this->load->model('PengumumanModel', 'pengumumanmodel');
        $this->load->model('HomeModel', 'homemodel');

        // Catatan: view homepage memuat sendiri CSS/JS-nya (assets/desain/home.css & home.js)
        // karena halaman depan tidak memakai layout umum/includes/layout.
        $this->load->js(base_url("assets/desain/home.js?v=1.0.0"));
    }

    public function index() {
        $berita = $this->beritamodel->getAllBerita();
        $pengumuman = $this->pengumumanmodel->getAllPengumuman();
        $sk = $this->skmodel->getAllSk();

        $this->load->view('homepage', array(
            'home'       => $this->homemodel,
            'berita'     => $berita,
            'pengumuman' => $pengumuman,
            'sk'         => $sk,
            'statistik'  => $this->homemodel->statistik(),
        ));
    }

}
