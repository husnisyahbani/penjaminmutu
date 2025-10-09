<?php

class Konsultasi extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->js(base_url("assets/app/umum/konsultasi.js"));
        $this->load->model('KonsultasiModel', 'konsultasi');
    }

    public function index() {
        $submitbutton = $this->input->post('submitbutton');
        if (isset($submitbutton) && $this->check_captcha()) {
            $data = array();
            $config['upload_path'] = 'filedata';
            $config['allowed_types'] = '*';

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('upload_file')) {
                $datagambar['upload_file'] = $this->upload->data();
                $link = $datagambar['upload_file']['file_name'];
                $data['konsul_file'] =  $link;
            }
            $data['konsul_judul'] = trim($this->input->post('konsul_judul'));
            $data['konsul_email'] = trim($this->input->post('konsul_email'));
            $data['konsul_isi'] = trim($this->input->post('konsul_isi'));
            
            if($this->konsultasi->add($data)){
                $msg = 'Berhasil';
                $this->session->set_flashdata('pesanberhasil', $msg);
            }else{
                $msg = 'Gagal';
                $this->session->set_flashdata('pesanerror', $msg);
            }
            redirect(base_url($this->module.'/konsultasi'));
        } else {

            $config_captcha = array(
                'img_path' => './captcha/',
                'img_url' => base_url().'captcha/',
                'font_path'     => FCPATH.'system/fonts/texb.ttf',
                'img_width'     => '250',
                'img_height'    => 70,
                'expiration'    => 7200,
                'word_length'   => 4,
                'font_size'     => 40,
                'colors'        => array(
                        'background' => array(255, 255, 255),
                        'border' => array(255, 255, 255),
                        'text' => array(0, 0, 0),
                        'grid' => array(255, 40, 40)
                )
            );

            $captcha = create_captcha($config_captcha);

            if (isset($this->session->userdata['image']) && file_exists(BASEPATH . "../captcha/" . $this->session->userdata['image']))
            unlink(BASEPATH . "../captcha/" . $this->session->userdata['image']);

            $this->session->unset_userdata('captchaCode');
            $this->session->unset_userdata('image');

            $this->session->set_userdata(array('captchaCode' => $captcha['word'], 'image' => $captcha['time'] . '.jpg'));

            $this->data['captcha'] = $captcha['image'];
            $this->data['content'] = 'konsultasi';
            $this->data['title'] = 'Kosultasi';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module);
        }
    }

    function check_captcha() {
        if ($this->input->post('captcha') == $this->session->userdata('captchaCode')) {
            return true;
        } else {
            $msg = 'Kode Captcha Salah';
            $this->session->set_flashdata('pesanerror', $msg);
            return false;
        }
    }


}
