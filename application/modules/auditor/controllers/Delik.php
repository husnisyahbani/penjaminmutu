<?php

class Delik extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/delik.js?v=2.0"));
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
            $this->data['title'] = 'Daftar Tilik';
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

    /**
     * Nilai satu butir tilik (untuk modal edit) + teks butirnya.
     */
    public function gettilik($jwb_id = NULL, $lingkup_id = NULL) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $this->load->model('LingkupModel', 'lingkup');
        $butir = $this->lingkup->butirSatu($lingkup_id);

        $nilai = $this->dtjwb->getNilaiByLingkup($jwb_id, $lingkup_id);
        if (!$nilai) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Butir tilik tidak ditemukan.'));
            return;
        }

        $nilai['status']      = TRUE;
        $nilai['lingkup_isi'] = $butir ? $butir['lingkup_isi'] : '';
        echo json_encode($nilai);
    }

    /**
     * Simpan Hasil / Temuan / Catatan satu butir lingkup.
     * Data: jwb_id, lingkup_id, kolom (jwb_hasil|jwb_temuan|jwb_catatan), nilai
     */
    public function simpantilik() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $jwb_id     = $this->input->post('jwb_id');
        $lingkup_id = $this->input->post('lingkup_id');
        $kolom      = $this->input->post('kolom');
        $nilai      = $this->input->post('nilai');

        $diizinkan = array('jwb_hasil', 'jwb_temuan', 'jwb_catatan');
        if (!in_array($kolom, $diizinkan, TRUE)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Kolom tidak dikenal.'));
            return;
        }

        // Temuan memakai pilihan tetap.
        if ($kolom === 'jwb_temuan' && $nilai !== '' && !in_array($nilai, array('S', 'OB', 'TS MINOR', 'TS MAYOR'), TRUE)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Temuan tidak dikenal.'));
            return;
        }

        $status = $this->dtjwb->simpanNilai($jwb_id, $lingkup_id, $kolom, $nilai);
        echo json_encode($status
            ? array('status' => TRUE, 'pesan' => 'Tersimpan.')
            : array('status' => FALSE, 'pesan' => 'Gagal menyimpan.'));
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
            $row[] = html_escape($field->lingkup_isi);
            $row[] = $field->jwb_hasil;
            $row[] = $field->jwb_temuan ? '<span class="badge badge-warning">' . html_escape($field->jwb_temuan) . '</span>' : '';
            $row[] = $field->jwb_catatan;

            // Tombol per butir: isi Hasil / Tetapkan Temuan / Isi Catatan.
            $aksi = '<button type="button" class="edithasil btn btn-sm btn-icon btn-primary"'
                . ' data-info="Isi hasil penilaian butir ini" lingkup_id="' . $field->lingkup_id . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button>'
                . ' <button type="button" class="edittemuan btn btn-sm btn-icon btn-warning"'
                . ' data-info="Tetapkan temuan butir ini" lingkup_id="' . $field->lingkup_id . '">'
                . '<i class="icon md-flag" aria-hidden="true"></i></button>'
                . ' <button type="button" class="editcatatan btn btn-sm btn-icon btn-success"'
                . ' data-info="Isi catatan/tindak lanjut butir ini" lingkup_id="' . $field->lingkup_id . '">'
                . '<i class="icon md-comment" aria-hidden="true"></i></button>';
            $row[] = '<div class="tabel-aksi">' . $aksi . '</div>';
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
