<?php

class Cekpermohonan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->js(base_url("assets/app/umum/cekpermohonan.js"));
    }

    public function index() {
        $submitcek = $this->input->post('submitcek');
        $no_test = $this->input->post('no_test');
        if(isset($submitcek)){
            $id = intval($no_test);
            redirect(base_url("hasil?id=".$id));
        }else{
            $this->data['content'] = 'cekpermohonan';
            $this->data['title'] = 'Cek Permohanan Izin Penelitian';
            $this->data['js'] = $this->load->get_js_files();
            $this->template($this->data, $this->module);
        }
        
    }

}
