<?php

class Delik extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/delik.js?v=1.15"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');
        $this->load->model('DtjwbModel', 'dtjwb');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $audit_id = $this->input->get('audit_id');
        $dtform_id = $this->input->get('dtform_id');
        if(isset($audit_id) && isset($dtform_id)){
            $this->data['content'] = 'delik/indexnew';
            $this->data['title'] = 'Daftar Tilik';
            $this->data['audit_id'] = $audit_id;
            $this->data['dtform_id'] = $dtform_id;
            $this->data['jwb_id'] = $this->dtjwb->getJwbid($audit_id,$dtform_id);
            $this->data['result'] = $this->mutu->getAuditById($audit_id);
            $this->data['jawab'] = $this->auditjawab->getAuditJawab($audit_id,$dtform_id);
            $this->data['js'] = $this->load->get_js_files();
            $this->data['audit'] = 'active';//auditmenu
            $this->data['auditmenu'] = 'active';
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
            
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

    public function referensi() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_referensi'] = $this->input->post('jwb_referensi');
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

    public function pertanyaan() {
        
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_pertanyaan'] = $this->input->post('jwb_pertanyaan');
        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $data['jwb_pertanyaan'] = strip_tags($data['jwb_pertanyaan'], $allowed_tags);
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
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_catatan'] = $this->input->post('jwb_catatan');
        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $data['jwb_catatan'] = strip_tags($data['jwb_catatan'], $allowed_tags);
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
        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $data['jwb_temuan'] = strip_tags($data['jwb_temuan'], $allowed_tags);
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
        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $data['jwb_hasil'] = strip_tags($data['jwb_hasil'], $allowed_tags);
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
            $row[] = '<input type="checkbox" class="pilih" value="'.$field->dtjwb_id.'"> '.$no;
            $row[] = '<span style="font-size:16px;font-weight:bold">PERTANYAAN:</span><br/>'.$field->dtjwb_pertanyaan . '<br/><span style="font-size:16px;font-weight:bold">REFERENSI:</span><br/>'.$field->dtjwb_referensi;
            $row[] = $field->dtjwb_hasil;
            $row[] = $field->dtjwb_temuan;
            $row[] = $field->dtjwb_catatan;
            
           
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
