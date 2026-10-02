<?php

class Detailform extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/detailform.js?v=3.0"));
        // Tampilan topik & activity (buka-tutup + pencarian).
        $this->load->js(base_url("assets/app/topik-aktivitas.js?v=1.0"));
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

            /* Tampilan gaya topik/activity: topik = pertanyaan, activity = butir
               lingkup. Seluruhnya dirender sekaligus (bukan tabel). */
            $topik = $this->dtform->getByFormId($form_id);
            $ids = array();
            foreach ($topik as $t) {
                $ids[] = $t['dtform_id'];
            }
            $this->data['topik'] = $topik;
            $this->data['peta_lingkup'] = $this->lingkup->peta($ids);
            $this->data['urut_siap'] = $this->dtform->urutSiap();
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

                // Pertanyaan baru diletakkan di urutan paling belakang.
                $data['dtform_urut'] = $this->dtform->urutBerikut($data['form_id']);

                if ($this->dtform->add($data)) {
                    $dtform_id = $this->db->insert_id();

                    // Butir lingkup hanya disentuh bila daftarnya memang dikirim
                    // (tampilan topik/activity menyimpan butir satu per satu).
                    if ($this->input->post('lingkup_isi') !== NULL) {
                        $simpan = $this->lingkup->simpan(
                            $dtform_id,
                            $this->input->post('lingkup_id'),
                            $this->input->post('lingkup_isi')
                        );
                        $pesan = "Berhasil, " . $simpan['tersimpan'] . " butir lingkup disimpan.";
                    } else {
                        $pesan = "Pertanyaan berhasil ditambahkan.";
                    }

                    $query = array(
                        "status" => true,
                        "pesan"  => $pesan,
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
                    if ($this->input->post('lingkup_isi') !== NULL) {
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
                    } else {
                        $pesan = "Pertanyaan berhasil diperbarui.";
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
        if (empty($id)) {
            $this->_json(array('status' => false, 'pesan' => 'Pertanyaan tidak diketahui.'));
            return;
        }

        // Butir lingkup dihapus lebih dahulu (relasi jawaban dilepas).
        $this->lingkup->hapusByDtform($id);
        $this->dtform->hapus($id);

        $this->_json(array('status' => true, 'pesan' => 'Pertanyaan berhasil dihapus.'));
    }

    /* =====================================================================
     | PERTANYAAN & LINGKUP
     | Satu pertanyaan (topik) memuat satu atau lebih lingkup (butir).
     * ================================================================== */

    /** Siapkan kolom urutan pertanyaan (dipakai tombol "Aktifkan Urutan Pertanyaan"). */
    public function pasangurut() {
        try {
            $siap = $this->dtform->installUrut();
            $this->_json(array(
                'status' => (bool) $siap,
                'pesan'  => $siap ? 'Urutan pertanyaan berhasil disiapkan.' : 'Kolom urutan gagal disiapkan.',
            ));
        } catch (Exception $e) {
            $this->_json(array('status' => false, 'pesan' => 'Gagal menyiapkan urutan: ' . $e->getMessage()));
        }
    }

    /** Geser pertanyaan (topik) naik/turun. */
    public function pindah() {
        $dtform_id = $this->input->post('dtform_id');
        $arah      = $this->input->post('arah');

        if ($this->dtform->pindah($dtform_id, $arah === 'turun' ? 'turun' : 'naik')) {
            $this->_json(array('status' => true, 'pesan' => 'Urutan pertanyaan diperbarui.'));
        } else {
            $this->_json(array('status' => false, 'pesan' => 'Pertanyaan sudah berada di ujung urutan.'));
        }
    }

    /** Geser butir lingkup naik/turun di dalam pertanyaannya. */
    public function pindahbutir() {
        $lingkup_id = $this->input->post('lingkup_id');
        $arah       = $this->input->post('arah');

        if ($this->lingkup->pindah($lingkup_id, $arah === 'turun' ? 'turun' : 'naik')) {
            $this->_json(array('status' => true, 'pesan' => 'Urutan lingkup diperbarui.'));
        } else {
            $this->_json(array('status' => false, 'pesan' => 'Lingkup sudah berada di ujung urutan.'));
        }
    }

    /** Simpan satu lingkup (butir): tambah baru atau ubah. */
    public function simpanbutir() {
        $dtform_id  = (int) $this->input->post('dtform_id');
        $lingkup_id = (int) $this->input->post('lingkup_id');
        $isi        = $this->input->post('lingkup_isi');

        if (empty($dtform_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Pertanyaan tidak diketahui.'));
            return;
        }

        $hasil = $this->lingkup->simpanSatu($dtform_id, $lingkup_id, $isi);
        $this->_json($hasil);
    }

    /** Hapus satu activity (butir lingkup). */
    public function hapusbutir() {
        $lingkup_id = (int) $this->input->post('lingkup_id');
        if (empty($lingkup_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Lingkup tidak diketahui.'));
            return;
        }

        $ditahan = array();
        if ($this->lingkup->hapusButir($lingkup_id, $ditahan)) {
            $this->_json(array('status' => true, 'pesan' => 'Lingkup berhasil dihapus.'));
        } else {
            $this->_json(array(
                'status' => false,
                'pesan'  => 'Lingkup tidak dihapus karena sudah dipakai pada jawaban audit.',
            ));
        }
    }

    /** Balas JSON (dipakai endpoint gaya topik/activity). */
    private function _json($data) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($data);
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
