<?php

class Detailform extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/detailform.js?v=1.6"));
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('FormulirModel', 'formulir');
        $this->load->model('LingkupModel', 'lingkup');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $form_id = $this->input->get('id');
        if(isset($form_id)){
            $this->data['content'] = 'detailform/index';
            $this->data['title'] = 'Detail Formulir';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['auditmenu'] = 'active';
            $this->data['formaudit'] = 'active';
            $this->data['formulir'] = $this->formulir->getFormulir($form_id);
            $this->data['form_id'] = $form_id;
            /* Kolom lama masih ada dan belum semua pertanyaan punya butir ->
               tawarkan halaman migrasi. */
            $this->data['perlu_migrasi'] = $this->lingkup->kolomLamaAda()
                && $this->lingkup->siap()
                && $this->lingkup->hitung() < $this->dtform->count_all($form_id);
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module);
        }else{
            redirect(base_url('formaudit'));
        }
        
    }

   public function tambah() {
        $dtform_pertanyaan = $this->input->post('dtform_pertanyaan');
        if($dtform_pertanyaan){
                $data = array();
                $data['dtform_pertanyaan'] = $dtform_pertanyaan;
                // Kolom lama hanya diisi bila strukturnya masih ada (belum dimigrasi).
                if ($this->lingkup->kolomLamaAda()) {
                    $data['dtform_lingkup'] = '';
                }
                $data['form_id']   = $this->input->post('form_id');

                if ($this->dtform->add($data)) {
                    // Butir lingkup disimpan di tabel lingkup.
                    $dtform_id = $this->db->insert_id();
                    $simpan = $this->lingkup->simpan(
                        $dtform_id,
                        $this->input->post('lingkup_id'),
                        $this->input->post('lingkup_isi')
                    );
                    $query = array(
                        "status" => true,
                        "pesan"  => "Berhasil, " . $simpan['tersimpan'] . " butir lingkup disimpan.",
                        "dtform_id" => $dtform_id,
                    );
                } else {
                    $query = array("status" => false, "pesan" => "Gagal");
                }

                header('Access-Control-Allow-Origin: *');
                header('Content-Type: application/json');
                echo json_encode($query);

        }else{
            $query = array("status" => false, "pesan" => "Silahkan mengisi form audit");
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        } 
    }

    public function edit() {
        $dtform_id = $this->input->post('dtform_id');
        if($dtform_id){
                $data = array();
                $data['dtform_pertanyaan'] = $this->input->post('dtform_pertanyaan');
                if ($this->lingkup->kolomLamaAda()) {
                    $data['dtform_lingkup'] = '';
                }
                $data['form_id']   = $this->input->post('form_id');
                $data['dtform_id'] = $dtform_id;

                if ($this->dtform->edit($data)) {
                    $simpan = $this->lingkup->simpan(
                        $dtform_id,
                        $this->input->post('lingkup_id'),
                        $this->input->post('lingkup_isi')
                    );

                    $pesan = "Berhasil, " . $simpan['tersimpan'] . " butir lingkup disimpan.";
                    if ($simpan['dihapus'] > 0) {
                        $pesan .= " " . $simpan['dihapus'] . " butir dihapus.";
                    }
                    if (!empty($simpan['ditahan'])) {
                        $pesan .= " " . count($simpan['ditahan'])
                                . " butir tidak dihapus karena sudah dipakai jawaban audit.";
                    }
                    $query = array("status" => true, "pesan" => $pesan);
                } else {
                    $query = array("status" => false, "pesan" => "Gagal");
                }

                header('Access-Control-Allow-Origin: *');
                header('Content-Type: application/json');
                echo json_encode($query);

        }else{
            $query = array("status" => false, "pesan" => "Silahkan mengisi form audit");
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        } 
    }

    public function getdtformById($id) {
        if(isset($id)){
            $query = $this->dtform->getdetailform($id);
            $query['status'] = true;
            $query['lingkup'] = $this->lingkup->butir($id);
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        }else{
            $query = array("status"=>false);
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query); 
        }
    }

    public function hapus() {
        $id = $this->input->post('id');
        // Butir lingkup dihapus lebih dahulu (relasi jawaban dilepas).
        $this->lingkup->hapusByDtform($id);
        $this->dtform->hapus($id);
    }

    public function listdtform($id) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->dtform->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->dtform_pertanyaan;
            $row[] = $this->lingkup->ringkas($field->dtform_id);
            $row[] = '</button><button class="edit btn btn-sm btn-icon btn-pure btn-default on-default edit-row"
            data-toggle="tooltip" data-original-title="Edit" id=' . $field->dtform_id . '><i class="icon md-edit" aria-hidden="true"></i></button><button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->dtform_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->dtform->count_all($id),
            "recordsFiltered" => $this->dtform->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
