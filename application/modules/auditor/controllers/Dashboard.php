<?php

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/formaudit.js?v=2.0"));
        $this->load->model('FormulirModel', 'formulir');
        $this->load->model('PeriodeModel', 'periode');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITOR') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content'] = 'dashboard/index';
        $this->data['title'] = 'Formulir Audit';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['dashboard'] = 'active';
        /* Filter periode: bawaan = periode aktif; bila tidak ada periode
           aktif, seluruh formulir ditampilkan. */
        // Bila kolom relasi belum dipasang (database lama), filter tidak
        // ditampilkan supaya tidak ada pilihan yang tidak berfungsi.
        $this->data['list_periode'] = $this->periode->formulirSiap() ? $this->periode->getAll() : array();
        $this->data['periode_aktif'] = $this->periode->aktifId();
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

   public function tambah() {
        $form_nama = $this->input->post('form_nama');
        if($form_nama){
                $data = array();
                $data['form_nama']  = strtoupper($form_nama);
                $data['form_kode'] = $this->input->post('form_kode');
                $data['form_deskripsi']   = $this->input->post('form_deskripsi');

                // Periode formulir: mengikuti pilihan pengguna. Bila pemilihnya
                // tidak dikirim sama sekali (mis. klien lama), model akan
                // memakai periode aktif.
                if ($this->formulir->periodeSiap()) {
                    $periode_id = $this->input->post('periode_id');
                    if ($periode_id !== NULL) {
                        $data['periode_id'] = ($periode_id === '') ? NULL : (int) $periode_id;
                    }
                }

                if ($this->formulir->add($data)) {
                    $query = array("status" => true, "pesan" => "Berhasil");
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
        $form_id = $this->input->post('form_id');
        if($form_id){
                $data = array();
                $data['form_nama']  = strtoupper($this->input->post('form_nama'));
                $data['form_kode'] = $this->input->post('form_kode');
                $data['form_deskripsi']   = $this->input->post('form_deskripsi');
                $data['form_id'] = $form_id;

                // Pindah periode / lepas ke "Tanpa Periode" (kosong = NULL).
                if ($this->formulir->periodeSiap()) {
                    $periode_id = $this->input->post('periode_id');
                    if ($periode_id !== NULL) {
                        $data['periode_id'] = ($periode_id === '') ? NULL : (int) $periode_id;
                    }
                }

                if ($this->formulir->edit($data)) {
                    $query = array("status" => true, "pesan" => "Berhasil");
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

    public function getFormulirById($id) {
        if(isset($id)){
            $query = $this->formulir->getFormulir($id);
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

    public function hapus() {
        $id = $this->input->post('id');
        $this->formulir->hapus($id);
    }

    public function listformulir() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');

        /* Filter periode. Bawaannya periode aktif (dipilih di halaman);
           kosong berarti semua periode. */
        $periode_id = $this->input->post('periode_id');
        if ($periode_id === NULL || $periode_id === '') {
            $periode_id = NULL;
        }

        $list = $this->formulir->get_datatables($post['length'], $post['start'], $post['search'], $post['order'], $periode_id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = html_escape($field->form_nama);
            $row[] = html_escape($field->form_kode);
            $row[] = $field->form_deskripsi;
            // Kolom periode: kolom relasi mungkin belum dipasang.
            if (isset($field->periode_id) && !empty($field->periode_id)) {
                $row[] = '<span class="badge badge-info">' . html_escape(isset($field->periode_tahun) ? $field->periode_tahun : $field->periode_id) . '</span>';
            } else {
                $row[] = '<span class="text-muted">-</span>';
            }
            $row[] = date("d-m-Y H:i:s", strtotime($field->form_create));

            $aksi = '<button type="button" class="detail btn btn-sm btn-icon btn-primary"'
                . ' data-info="Lihat daftar pertanyaan formulir ini" id="' . $field->form_id . '">'
                . '<i class="icon md-book" aria-hidden="true"></i></button>'
                . ' <button type="button" class="edit btn btn-sm btn-icon btn-success"'
                . ' data-info="Ubah nama, kode, deskripsi, atau periode formulir" id="' . $field->form_id . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button>'
                . ' <button type="button" class="delete btn btn-sm btn-icon btn-danger"'
                . ' data-info="Hapus formulir beserta pertanyaannya" id="' . $field->form_id . '">'
                . '<i class="icon md-delete" aria-hidden="true"></i></button>';
            $row[] = '<div class="tabel-aksi">' . $aksi . '</div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->formulir->count_all($periode_id),
            "recordsFiltered" => $this->formulir->count_filtered($post['search'], $post['order'], $periode_id),
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
