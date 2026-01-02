<?php

class Homepage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('BeritaModel', 'beritamodel');
        $this->load->model('PengumumanModel', 'pengumumanmodel');
        $this->load->js(base_url("assets/app/umum/login.js"));
    }

    public function index() {    
                // $this->data['content'] = 'homepage';
                // $this->data['title'] = 'homepage';
                // $this->data['js'] = $this->load->get_js_files();
                // $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
                // $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
                // $this->template($this->data, $this->module);
                $berita = $this->beritamodel->getAllBerita();
                $pengumuman = $this->pengumumanmodel->getAllPengumuman();
                $this->load->view('homepage', array('berita' => $berita, 'pengumuman' => $pengumuman));
    }

}
