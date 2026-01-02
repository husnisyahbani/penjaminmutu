<?php

class Pengumuman extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->js(base_url("assets/app/umum/pengumuman.js?v=1.0.1"));
        $this->load->model('PengumumanModel', 'pengumumanmodel');
    }

    public function index() {
        $pengumuman_id = $this->input->get('id');    
        $pengumuman = $this->pengumumanmodel->getPengumumanById($pengumuman_id);
        $result = $this->pengumumanmodel->getAllPengumuman();
        $this->data['js'] = $this->load->get_js_files();
        $this->load->view('baca_pengumuman', array('pengumuman' => $pengumuman, 'result' => $result));
    }

}
