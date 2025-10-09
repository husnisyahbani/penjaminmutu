<?php

class Izinpenelitian extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->js(base_url("assets/app/umum/izinpenelitian.js"));
        $this->load->model('IzinpenelitianModel', 'izinpenelitian');
    }

    private function convert($date) {
        if ($date != '') {
            $parts = explode('-',$date);
            $yyyy_mm_dd = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            return $yyyy_mm_dd;
        } else {
            return "";
        }
    }

    public function index() {
        $submitbutton = $this->input->post('submitbutton');
        if (isset($submitbutton) && $this->check_captcha()) {
            $data = array();
            $config['upload_path'] = 'filedata';
            $config['allowed_types'] = 'jpg|png|bmp|jpeg|pdf';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('upload_file')) {
                $datagambar['upload_file'] = $this->upload->data();
                $link = $datagambar['upload_file']['file_name'];
                $data['ip_file'] =  $link;

                $data['ip_nama'] = trim($this->input->post('ip_nama'));
                $data['ip_npm'] = trim($this->input->post('ip_npm'));
                $data['ip_email'] = trim($this->input->post('ip_email'));
                $data['ip_asalpt'] = trim($this->input->post('ip_asalpt'));
                $data['ip_fakultas'] = trim($this->input->post('ip_fakultas'));
                $data['ip_prodi'] = trim($this->input->post('ip_prodi'));
                $data['ip_tglsurat'] = $this->convert($this->input->post('ip_tglsurat'));
                $data['ip_nosurat'] = trim($this->input->post('ip_nosurat'));
                $data['ip_judul'] = trim($this->input->post('ip_judul'));
                $data['ip_lokasi'] = trim($this->input->post('ip_lokasi'));
                $data['ip_mulai'] = $this->convert($this->input->post('ip_mulai'));
                $data['ip_selesai'] = $this->convert($this->input->post('ip_selesai'));

                $tglmulai = date('Y-m-d',strtotime($data['ip_mulai'] ));
                $tglselesai = date('Y-m-d',strtotime($data['ip_selesai'] ));

                if($tglselesai > $tglmulai){
                    $id = $this->izinpenelitian->add_id($data);
                    if(isset($id))
                    {
                        $msg = 'Berhasil';
                        $this->session->set_flashdata('pesanberhasil', $msg);
                        redirect(base_url($this->module.'/hasil/noantrian/'.$id));
                    }else{
                        $msg = 'Gagal';
                        $this->session->set_flashdata('pesanerror', $msg);
                        redirect(base_url($this->module.'/izinpenelitian'));
                    }
                }else{
                    $msg = 'Tanggal Selesai penelitian harus setelah tanggal mulai penelitian';
                    $this->session->set_flashdata('pesanerror', $msg);
                    redirect(base_url($this->module.'/izinpenelitian'));
                } 

                
            }else{
                $msg = 'Format file salah';
                $this->session->set_flashdata('pesanerror', $msg);
                redirect(base_url($this->module.'/izinpenelitian'));
            }

            
            
            
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
             $this->data['content'] = 'izinpenelitian';
             $this->data['title'] = 'Permohonan Penelitan';
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
