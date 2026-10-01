<?php

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditee';
        $this->load->js(base_url("assets/app/auditee/daftaraudit.js?v=1.35"));
        // Informasi tombol aksi saat hover (lihat assets/app/tabel-aksi.css)
        $this->load->js(base_url("assets/app/tabel-aksi.js?v=1.0"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');
        $this->load->model('PeriodeModel', 'periode');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITEE') {
            redirect(base_url());
        }
    }

    public function index() {
            $this->data['content'] = 'dashboard/index';
            $this->data['title'] = 'Daftar Audit';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['dashboard'] = 'active';
            $this->data['totalterkirim'] = $this->mutu->totalTerkirim();
            $this->data['totalproses'] = $this->mutu->totalProses();
            $this->data['totalselesai'] = $this->mutu->totalSelesai();
            $this->data['totaldraft'] = $this->mutu->totalDraft();
            $this->data['listauditor'] = $this->akun->getAllAuditor();
            $this->data['listauditee'] = $this->akun->getAllAuditee();
            $this->data['formulir'] = $this->formulir->getAllFormulir();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
    }

    public function detail($id) {
        if(isset($id)){
            $this->data['content'] = 'dashboard/detail';
            $this->data['title'] = 'Daftar Audit';
            $this->data['audit_id'] = $id;
            $this->data['dashboard'] = 'active';
            $this->data['result'] = $this->mutu->getAuditById($id);
            $this->data['js'] = $this->load->get_js_files();
            $this->data['audit'] = 'active';
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
        }
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

                // Audit baru ditempatkan pada periode yang sedang aktif (bila ada).
                if ($this->periode->siap()) {
                    $data['periode_id'] = $this->periode->aktifId();
                }

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

    public function jawab() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_jawaban'] = $this->input->post('jwb_jawaban');
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

    public function update() {
        $data = array();
        $data['audit_id'] = $this->input->post('id');
        $data['audit_status'] = "TERKIRIM";
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

            /* ================= TOMBOL AKSI =================
               Pola seragam seperti halaman auditor: btn btn-sm btn-icon
               btn-<warna> + ikon saja, keterangan muncul saat kursor
               diarahkan (data-info diolah assets/app/tabel-aksi.js).
               Dibungkus .tabel-aksi supaya selalu satu baris. */
            $btn_detail = '<button type="button" class="detail btn btn-sm btn-icon btn-primary" '
                . 'data-info="Lihat rincian dan jawab pertanyaan" '
                . 'aria-label="Detail" id="' . $field->audit_id . '">'
                . '<i class="icon md-book" aria-hidden="true"></i></button>';

            $btn_kirim = '<button type="button" class="kirim btn btn-sm btn-icon btn-success" '
                . 'data-info="Kirim hasil evaluasi ke auditor" '
                . 'aria-label="Kirim" id="' . $field->audit_id . '">'
                . '<i class="icon md-mail-send" aria-hidden="true"></i></button>';

            // ---- kolom Aksi: seluruh tombol, sesuai status ----
            $aksi = $btn_detail;
            if ($field->audit_status == "DRAFT") {
                $aksi .= $btn_kirim;   // auditee masih dapat mengirim hasil
            }

            $row[] = '<div class="tabel-aksi">' . $aksi . '</div>';

            // ---- kolom Status: hanya badge (tanpa tombol) ----
            $badge = array(
                'DRAFT'    => 'badge-default',
                'TERKIRIM' => 'badge-info',
                'PROSES'   => 'badge-warning',
                'SELESAI'  => 'badge-success',
            );

            $kunci = strtoupper(trim($field->audit_status));
            $kelas = isset($badge[$kunci]) ? $badge[$kunci] : 'badge-default';
            $row[] = '<span class="badge ' . $kelas . '">' . ucfirst(strtolower($kunci)) . '</span>';
            
            
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
            $row[] = $field->dtform_pertanyaan."<br/>".$field->dtform_lingkup;
            $row[] = ' <button type="button" class="delik btn btn-warning btn-xs waves-effect waves-classic" dtform_id=' . $field->dtform_id.' audit_id=' . $field->audit_id.'><i class="icon md-edit" aria-hidden="true"></i>Jawaban dan Delik</button>';
           
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


    public function password() {
        $submitpass = $this->input->post('submitpass');
        if (isset($submitpass)) {
           $data = array();
           $passlama = md5($this->input->post('passlama'));
           
           $data['users_id'] = $this->session->userdata('users_id');
           $data['username'] = $this->session->userdata('username');
           $data['password'] = md5($this->input->post('password'));
           if($this->akun->passlama($passlama)&&$this->akun->edit($data)){
               $msg = 'Berhasil';
               $this->session->set_flashdata('pesanberhasil', $msg);
           }else{
               $msg = 'Gagal, password lama salah';
               $this->session->set_flashdata('pesanerror', $msg);
           }
           redirect(base_url($this->module));
       } else {
           $this->data['content'] = 'password';
           $this->data['title'] = 'Ubah Password';
           $this->data['js'] = $this->load->get_js_files();
           $this->template($this->data, $this->module);
       }
   }

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }


}
