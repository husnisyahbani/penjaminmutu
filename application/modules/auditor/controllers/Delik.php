<?php

class Delik extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/delik.js?v=1.32"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('DtjwbModel', 'dtjwb');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITOR') {
            redirect(base_url());
        }
    }

    public function index() {
        $audit_id = $this->input->get('audit_id');
        $dtform_id = $this->input->get('dtform_id');
        if(isset($audit_id) && isset($dtform_id)){
            $this->data['content'] = 'delik/indexnew';
            $this->data['title'] = 'Delik';
            $this->data['audit_id'] = $audit_id;
            $this->data['dtform_id'] = $dtform_id;
            $this->data['jwb_id'] = $this->dtjwb->getJwbid($audit_id,$dtform_id);
            $this->data['result'] = $this->mutu->getAuditById($audit_id);
            $this->data['jawab'] = $this->auditjawab->getAuditJawab($audit_id,$dtform_id);
            $this->data['js'] = $this->load->get_js_files();
            $this->data['audit'] = 'active';
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
            
        }
    }

    public function getjawabById($id) {
        if(isset($id)){
            $query = $this->dtjwb->getjawabById($id);
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

    public function tambahtilik() {
        $data = array();
        $data['jwb_id'] = $this->input->post('jwb_id');
        $data['dtjwb_pertanyaan'] = $this->input->post('dtjwb_pertanyaan');
        $data['dtjwb_referensi'] = $this->input->post('dtjwb_referensi');
        $status = $this->dtjwb->add($data);
        
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }


    public function pertanyaan() {
        
        $data = array();
        $data['dtjwb_pertanyaan'] = $this->input->post('edit_dtjwb_pertanyaan');
        $data['dtjwb_id'] = $this->input->post('pertanyaan_dtjwb_id');
        $status = $this->dtjwb->edit($data);
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function hasil() {
        $data = array();
        $data['dtjwb_hasil'] = $this->input->post('edit_dtjwb_hasil');
        $data['dtjwb_id'] = $this->input->post('hasil_dtjwb_id');
        $status = $this->dtjwb->edit($data);
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }


    public function temuan() {
        $data = array();
        $data['dtjwb_temuan'] = $this->input->post('edit_dtjwb_temuan');
        $data['dtjwb_id'] = $this->input->post('temuan_dtjwb_id');
        $status = $this->dtjwb->edit($data);
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function tujuan() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_tujuan'] = $this->input->post('jwb_tujuan');
        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $data['jwb_tujuan'] = strip_tags($data['jwb_tujuan'], $allowed_tags);
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

    public function catatan() {
        $data = array();
        $data['dtjwb_catatan'] = $this->input->post('edit_dtjwb_catatan');
        $data['dtjwb_id'] = $this->input->post('catatan_dtjwb_id');
        $status = $this->dtjwb->edit($data);
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }


    public function hapus() {
        $data = array();
        $dtjwb_id = $this->input->post('id');
        $status = $this->dtjwb->hapus($dtjwb_id);
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }


    

    public function listdelik($id) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->dtjwb->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<span style="font-size:16px;font-weight:bold">PERTANYAAN:</span><br/>'.$field->dtjwb_pertanyaan . '<br/><span style="font-size:16px;font-weight:bold">REFERENSI:</span><br/>'.$field->dtjwb_referensi.'<br/><button class="editpertanyaan btn btn-sm btn-icon btn-pure btn-default on-default"
            data-toggle="tooltip" data-original-title="Pertanyaan" id=' . $field->dtjwb_id . '><i class="icon md-edit" aria-hidden="true"></i></button>';
            $row[] = $field->dtjwb_hasil . '<button class="edithasil btn btn-sm btn-icon btn-pure btn-default on-default"
            data-toggle="tooltip" data-original-title="Hasil" id=' . $field->dtjwb_id . '><i class="icon md-edit" aria-hidden="true"></i></button>';
            $row[] = $field->dtjwb_temuan . '<button class="edittemuan btn btn-sm btn-icon btn-pure btn-default on-default"
            data-toggle="tooltip" data-original-title="Temuan" id=' . $field->dtjwb_id . '><i class="icon md-edit" aria-hidden="true"></i></button>';
            $row[] = $field->dtjwb_catatan . '<button class="editcatatan btn btn-sm btn-icon btn-pure btn-default on-default"
            data-toggle="tooltip" data-original-title="Catatan" id=' . $field->dtjwb_id . '><i class="icon md-edit" aria-hidden="true"></i></button>';
            $row[] = '<button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"
                      data-toggle="tooltip" data-original-title="Remove" id=' . $field->dtjwb_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->dtjwb->count_all($id),
            "recordsFiltered" => $this->dtjwb->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

}
