<?php

class Pengumuman extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/pengumuman.js?v=1.0.1"));
        $this->load->model('PengumumanModel', 'pengumumanmodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'pengumuman/index';
        $this->data['title'] = 'Pengumuman';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['pengumuman'] = 'active';
        $this->data['website'] = 'active';
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    private function limit_200_words($text){
    // Hilangkan spasi berlebih
    $text = trim(preg_replace('/\s+/', ' ', $text));

    $words = explode(' ', $text);
    $count = count($words);

    if($count <= 200){
        return $text; // tidak lebih dari 200 kata
    }

    // Ambil 200 kata pertama lalu tambah ...
    $limited = array_slice($words, 0, 200);
    return implode(' ', $limited) . ' ...';
}


    public function tambah() {
                $data = array();
                $config['upload_path'] = 'filedata';
                $config['allowed_types'] = '*';
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                
                if ($this->upload->do_upload('upload_file')) {
                    $datagambar['upload_file'] = $this->upload->data();
                    $link = $datagambar['upload_file']['file_name'];
                    $data['pengumuman_file'] =  $link;

                    $data['pengumuman_judul']  = $this->input->post('pengumuman_judul');
                    $data['pengumuman_deskripsi'] = $this->input->post('pengumuman_deskripsi');
                    $data['pengumuman_isi'] = trim($this->input->post('pengumuman_isi'));

                    if($this->pengumumanmodel->add($data)){
                        $msg = 'Berhasil';
                        $this->session->set_flashdata('pesanberhasil', $msg);
                    }else{
                        $msg = 'Gagal';
                        $this->session->set_flashdata('pesanerror', $msg);
                    }
                    redirect(base_url($this->module."/pengumuman"));
                }else{
                    $this->data['content'] = 'pengumuman/add_pengumuman';
                    $this->data['title'] = 'Pengumuman';
                    $this->data['pengumuman'] = 'active';
                    $this->data['website'] = 'active';
                    $this->data['js'] = $this->load->get_js_files();
                    $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
                    $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
                    $this->template($this->data, $this->module);
                } 
    }

    public function baca($pengumuman_id = null) {
        $this->data['content'] = 'pengumuman/baca_pengumuman';
        $this->data['title'] = 'Pengumuman';
        $this->data['pengumuman'] = 'active';
        $this->data['website'] = 'active';
        $this->data['result'] = $this->pengumumanmodel->getPengumumanById($pengumuman_id);
        $this->data['js'] = $this->load->get_js_files();
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    public function edit($pengumuman_id = null) {
        $submitpengumuman = $this->input->post('submitpengumuman');
        if($submitpengumuman){
            $data = array();
            $config['upload_path'] = 'filedata';
            $config['allowed_types'] = '*';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('upload_file')) {
                $datagambar['upload_file'] = $this->upload->data();
                $link = $datagambar['upload_file']['file_name'];
                $data['pengumuman_file'] =  $link;
            }
            $data['pengumuman_id'] = $pengumuman_id;
            $data['pengumuman_judul']  = $this->input->post('pengumuman_judul');
            $data['pengumuman_deskripsi'] = $this->input->post('pengumuman_deskripsi');
            $data['pengumuman_isi'] = trim($this->input->post('pengumuman_isi'));
            
            if($this->pengumumanmodel->edit($data)){
                $msg = 'Berhasil';
                $this->session->set_flashdata('pesanberhasil', $msg);
            }else{
                $msg = 'Gagal';
                $this->session->set_flashdata('pesanerror', $msg);
            }

            redirect(base_url($this->module."/pengumuman"));

        }else{
            $this->data['content'] = 'pengumuman/edit_pengumuman';
            $this->data['title'] = 'Pengumuman';
            $this->data['pengumuman'] = 'active';
            $this->data['website'] = 'active';
            $this->data['result'] = $this->pengumumanmodel->getPengumumanById($pengumuman_id);
            $this->data['js'] = $this->load->get_js_files();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module);
        } 
    }

    public function hapus() {
        $id = $this->input->post('id');
        $this->pengumumanmodel->hapus($id);
    }

    public function listpengumuman($id = null) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->pengumumanmodel->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->pengumuman_judul;
            $row[] = $this->limit_200_words($field->pengumuman_deskripsi);
            $row[] = '<a class="btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="Detail" href="'.base_url('filedata/').$field->pengumuman_file.'" target="_blank"><i class="icon md-download" aria-hidden="true"></i> Download</a>' ;
            $row[] = date("d-m-Y H:i:s", strtotime($field->pengumuman_create));
            $row[] = '<button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->pengumuman_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->pengumuman_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->pengumumanmodel->count_all($id),
            "recordsFiltered" => $this->pengumumanmodel->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
