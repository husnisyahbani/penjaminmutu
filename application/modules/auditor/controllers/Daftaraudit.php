<?php

class Daftaraudit extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/daftaraudit.js?v=1.27"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITOR') {
            redirect(base_url());
        }
    }

    public function index() {
            $this->data['content'] = 'daftaraudit/index';
            $this->data['title'] = 'Daftar Audit';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['audit'] = 'active';
            $this->data['totalterkirim'] = $this->mutu->totalTerkirim();
            $this->data['totalterProses'] = $this->mutu->totalProses();
            $this->data['totalselesai'] = $this->mutu->totalSelesai();
            $this->data['totaldraft'] = $this->mutu->totalDraft();
            $this->data['listauditor'] = $this->akun->getAllAuditor();
            $this->data['listauditee'] = $this->akun->getAllAuditee();
            $this->data['formulir'] = $this->formulir->getAllFormulir();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
    }

   public function tambah() {
        $form_id = $this->input->post('form_id');
        if($form_id){
                $data = array();
                $data['form_id'] = $form_id;
                $data['auditor_id'] = $this->input->post('auditor');
                $data['auditee_id']  = $this->input->post('auditee');

                $akunauditee = $this->akun->getAkunById($data['auditee_id']);
                $data['auditee'] = $akunauditee['nama'];
                $data['unit'] = $akunauditee['unitkerja'];

                $akunauditor = $this->akun->getAkunById($data['auditor_id']);
                $data['auditor'] = $akunauditor['nama'];

                if ($this->mutu->add($data)) {
                    $query = array("status" => true, "pesan" => "Berhasil");
                } else {
                    $query = array("status" => false, "pesan" => "Gagal");
                }

                header('Access-Control-Allow-Origin: *');
                header('Content-Type: application/json');
                echo json_encode($query);

        }else{
            $query = array("status" => false, "pesan" => "Silahkan pilih form audit");
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        } 
    }

    public function getauditById($id) {
        if(isset($id)){
            $query = $this->mutu->getauditById($id);
            $query['status'] = true;
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

    public function catatan() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_catatan'] = $this->input->post('jwb_catatan');
        if($this->auditjawab->is_exist($data)){
            $status = $this->auditjawab->jawab($data);
        }else{
            $status = $this->auditjawab->add($data);
        }
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function temuan() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_temuan'] = $this->input->post('jwb_temuan');
        if($this->auditjawab->is_exist($data)){
            $status = $this->auditjawab->jawab($data);
        }else{
            $status = $this->auditjawab->add($data);
        }
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function hasil() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_hasil'] = $this->input->post('jwb_hasil');
        if($this->auditjawab->is_exist($data)){
            $status = $this->auditjawab->jawab($data);
        }else{
            $status = $this->auditjawab->add($data);
        }
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function proses() {
        $data = array();
        $data['audit_id'] = $this->input->post('id');
        $data['audit_status'] = "PROSES";
        $this->mutu->edit($data);
    }

    public function selesai() {
        $data = array();
        $data['audit_id'] = $this->input->post('id');
        $data['audit_status'] = "SELESAI";
        $this->mutu->edit($data);
    }

    public function hapus() {
        $id = $this->input->post('id');
        $this->mutu->hapus($id);
    }

    public function listmutu() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->mutu->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->form_nama;
            $row[] = $field->auditor;
            $row[] = $field->auditee;
            $row[] = $field->unit;

            if($field->audit_status == "PROSES" || $field->audit_status == "SELESAI"){
                $row[] = '<button class="detail btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="DETAIL" id=' . $field->audit_id.'><i class="icon md-book" aria-hidden="true"></i></button>';
            }else{
                $row[] = '<button class="btn btn-warning btn-xs waves-effect waves-classic"
            data-toggle="tooltip" data-original-title="Wait">Menunggu Proses</button>';
            }
            
            if($field->audit_status == "DRAFT"){
                $row[] = '<button class="btn btn-primary btn-xs waves-effect waves-classic"
            data-toggle="tooltip" data-original-title="DRAFT">Draft</button>';
            }else if($field->audit_status == "TERKIRIM"){                                        
                $row[] = '<button type="button" class="proses btn btn-sm btn-icon btn-warning" id="'.$field->audit_id.'"><i class="icon md-play" aria-hidden="true"></i>Proses</button>';
            }else if($field->audit_status == "PROSES"){
                $row[] = '<button type="button" class="selesai btn btn-sm btn-icon btn-danger" id="'.$field->audit_id.'"><i class="icon md-play" aria-hidden="true"></i>Selesai</button>';
            }else if($field->audit_status == "SELESAI"){
                $row[] = '<button type="button" class="btn btn-success btn-xs waves-effect waves-classic" id="'.$field->audit_id.'"><i class="icon md-download" aria-hidden="true"></i>Selesai</button>';
            }
            
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->mutu->count_all(),
            "recordsFiltered" => $this->mutu->count_filtered($post['search'], $post['order']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function listpertanyaan($id=null) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->auditjawab->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->dtform_pertanyaan;
            $row[] = $field->dtform_lingkup;
            $row[] = $field->jwb_jawaban;
            if($field->audit_status == "PROSES"){
                $row[] = $field->jwb_hasil.' <button type="button" class="hasil btn btn-warning btn-xs waves-effect waves-classic" dtform_id=' . $field->dtform_id.' audit_id=' . $field->audit_id.'><i class="icon md-edit" aria-hidden="true"></i></button>';
            }else{
                $row[] = $field->jwb_hasil;
            }

            if($field->audit_status == "PROSES"){
                $row[] = $field->jwb_temuan.' <button type="button" class="temuan btn btn-warning btn-xs waves-effect waves-classic" dtform_id=' . $field->dtform_id.' audit_id=' . $field->audit_id.'><i class="icon md-edit" aria-hidden="true"></i></button>';
            }else{
                $row[] = $field->jwb_temuan;
            }

            if($field->audit_status == "PROSES"){
                $row[] = $field->jwb_catatan.' <button type="button" class="catatan btn btn-warning btn-xs waves-effect waves-classic" dtform_id=' . $field->dtform_id.' audit_id=' . $field->audit_id.'><i class="icon md-edit" aria-hidden="true"></i></button>';
            }else{
                $row[] = $field->jwb_catatan;
            }
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->auditjawab->count_all($id),
            "recordsFiltered" => $this->auditjawab->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
