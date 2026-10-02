<?php

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditee';
        $this->load->js(base_url("assets/app/auditee/daftaraudit.js?v=2.1"));
        // Informasi tombol aksi saat hover (lihat assets/app/tabel-aksi.css)
        $this->load->js(base_url("assets/app/tabel-aksi.js?v=1.0"));
        // Tampilan topik & activity pada halaman detail audit.
        $this->load->js(base_url("assets/app/topik-aktivitas.js?v=1.0"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');
        $this->load->model('PeriodeModel', 'periode');
        $this->load->model('LampiranModel', 'lampiran');
        // Jawaban wajib + lampiran opsional per lingkup (halaman detail audit).
        $this->load->js(base_url("assets/app/auditee/jawaban-lingkup.js?v=1.0"));

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
            /* Pilihan formulir mengikuti periode aktif (formulir lama tanpa periode
               tetap ikut tampil). */
            $this->data['formulir'] = $this->formulir->getAllFormulir($this->periode->aktifId());
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
            /* Pertanyaan (topik) + lingkup (butir) beserta jawaban, lampiran,
               dan koreksi auditee untuk halaman detail audit. */
            $this->data['topik'] = $this->auditjawab->petaTilik($id);

            $status = strtoupper(trim((string) $this->data['result']['audit_status']));
            $this->data['sudah_terkirim'] = in_array($status, array('TERKIRIM', 'SELESAI'), TRUE);
            $this->data['lampiran_siap']  = $this->lampiran->siap();
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
        $id = $this->input->post('id');
        $audit = $this->mutu->getAuditById($id);

        if (!$audit) {
            $this->_json(array('status' => false, 'pesan' => 'Audit tidak ditemukan.'));
            return;
        }

        // Seluruh lingkup wajib dijawab sebelum hasil dikirim ke auditor.
        $kurang = $this->auditjawab->lingkupBelumDijawab($id);
        if (!empty($kurang)) {
            $contoh = array();
            foreach (array_slice($kurang, 0, 3) as $k) {
                $contoh[] = lingkup_bersihkan($k['lingkup_isi']);
            }
            $pesan = 'Masih ada ' . count($kurang) . ' lingkup yang belum dijawab.';
            if (!empty($contoh)) {
                $pesan .= ' Misalnya: ' . implode('; ', $contoh) . '.';
            }
            $this->_json(array(
                'status' => false,
                'pesan'  => $pesan,
                'belum'  => count($kurang),
            ));
            return;
        }

        $data = array();
        $data['audit_id'] = $id;
        $data['audit_status'] = "TERKIRIM";
        $this->mutu->edit($data);

        $this->_json(array('status' => true, 'pesan' => 'Hasil evaluasi terkirim.'));
    }

    /* =====================================================================
     | JAWABAN & LAMPIRAN PER LINGKUP
     | Halaman detail audit: tiap lingkup wajib dijawab, lampiran opsional
     | dan boleh lebih dari satu berkas.
     * ================================================================== */

    /** Simpan jawaban satu lingkup (halaman detail audit). */
    public function jawablingkup() {
        $audit_id  = (int) $this->input->post('audit_id');
        $lingkup_id = (int) $this->input->post('lingkup_id');

        if (empty($audit_id) || empty($lingkup_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Lingkup tidak diketahui.'));
            return;
        }

        $kunci = $this->_kunci($audit_id);
        if ($kunci !== TRUE) {
            $this->_json($kunci);
            return;
        }

        $hasil = $this->auditjawab->simpanJawabanLingkup($audit_id, $lingkup_id, $this->input->post('jwb_jawaban'));
        $this->_json($hasil);
    }

    /**
     * Unggah lampiran satu lingkup. Berkas boleh lebih dari satu
     * (input name="lampiran[]").
     */
    public function unggahlampiran() {
        $audit_id   = (int) $this->input->post('audit_id');
        $lingkup_id = (int) $this->input->post('lingkup_id');

        if (empty($audit_id) || empty($lingkup_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Lingkup tidak diketahui.'));
            return;
        }

        $kunci = $this->_kunci($audit_id);
        if ($kunci !== TRUE) {
            $this->_json($kunci);
            return;
        }

        if (!$this->lampiran->siap()) {
            $this->_json(array(
                'status' => false,
                'pesan'  => 'Fitur lampiran belum disiapkan (impor database/lampiran_lingkup.sql).',
            ));
            return;
        }

        if (!$this->auditjawab->lingkupAudit($audit_id, $lingkup_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Lingkup tidak ditemukan pada audit ini.'));
            return;
        }

        if (!isset($_FILES['lampiran']) || empty($_FILES['lampiran']['name'])) {
            $this->_json(array('status' => false, 'pesan' => 'Belum ada berkas yang dipilih.'));
            return;
        }

        $this->load->library('upload');

        $berkas  = $_FILES['lampiran'];
        $nama    = is_array($berkas['name']) ? $berkas['name'] : array($berkas['name']);
        $jumlah  = count($nama);

        $tersimpan = array();
        $gagal     = array();

        for ($i = 0; $i < $jumlah; $i++) {
            $nama_berkas = isset($nama[$i]) ? trim($nama[$i]) : '';
            if ($nama_berkas === '') {
                continue;
            }

            // Upload library hanya menangani satu berkas, jadi tiap berkas
            // disusun ulang menjadi satu entri $_FILES tersendiri.
            $_FILES['lampiran_berkas'] = array(
                'name'     => $nama_berkas,
                'type'     => is_array($berkas['type']) ? $berkas['type'][$i] : $berkas['type'],
                'tmp_name' => is_array($berkas['tmp_name']) ? $berkas['tmp_name'][$i] : $berkas['tmp_name'],
                'error'    => is_array($berkas['error']) ? $berkas['error'][$i] : $berkas['error'],
                'size'     => is_array($berkas['size']) ? $berkas['size'][$i] : $berkas['size'],
            );

            $config = array(
                'upload_path'   => $this->lampiran->folder(),
                'allowed_types' => 'pdf|doc|docx|xls|xlsx|ppt|pptx|jpg|jpeg|png|zip|rar',
                'encrypt_name'  => TRUE,
                'max_size'      => 5120,   // 5 MB per berkas
                'remove_spaces' => TRUE,
            );

            $this->upload->initialize($config);

            if ($this->upload->do_upload('lampiran_berkas')) {
                $info = $this->upload->data();
                $id   = $this->lampiran->tambah($audit_id, $lingkup_id, $info);

                $tersimpan[] = array(
                    'lampiran_id' => $id,
                    'nama'        => $info['orig_name'],
                    'url'         => $this->lampiran->url($info['file_name']),
                    'ukuran'      => $this->lampiran->ukuranTeks($info['file_size'] * 1024),
                    'tipe'        => ltrim($info['file_ext'], '.'),
                );
            } else {
                $gagal[] = $nama_berkas . ': ' . $this->_pesanGagalUnggah();
            }
        }

        if (empty($tersimpan)) {
            $this->_json(array(
                'status' => false,
                'pesan'  => empty($gagal) ? 'Belum ada berkas yang dipilih.' : implode(' | ', $gagal),
            ));
            return;
        }

        $pesan = count($tersimpan) . ' lampiran terunggah.';
        if (!empty($gagal)) {
            $pesan .= ' ' . count($gagal) . ' berkas gagal: ' . implode(' | ', $gagal);
        }

        $this->_json(array(
            'status'    => true,
            'pesan'     => $pesan,
            'lampiran'  => $tersimpan,
            'gagal'     => $gagal,
        ));
    }

    /**
     * Pesan kesalahan unggah berbahasa Indonesia (perpustakaan upload bawaan
     * CodeIgniter memakai pesan bahasa Inggris).
     */
    private function _pesanGagalUnggah() {
        $asli = strtolower(trim(preg_replace('/\s+/', ' ', strip_tags($this->upload->display_errors('', '')))));

        if (strpos($asli, 'not allowed') !== FALSE || strpos($asli, 'filetype') !== FALSE) {
            return 'jenis berkas tidak diizinkan (PDF, Office, gambar, atau arsip)';
        }
        if (strpos($asli, 'too large') !== FALSE || strpos($asli, 'exceeds') !== FALSE
                || strpos($asli, 'max_size') !== FALSE || strpos($asli, 'size') !== FALSE) {
            return 'ukuran berkas melebihi 5 MB';
        }
        if (strpos($asli, 'upload path') !== FALSE || strpos($asli, 'not writable') !== FALSE) {
            return 'folder penyimpanan lampiran tidak dapat ditulis';
        }
        if ($asli === '') {
            return 'berkas gagal diunggah';
        }
        return 'berkas gagal diunggah (' . $asli . ')';
    }

    /** Hapus satu lampiran milik audit auditee yang sedang dibuka. */
    public function hapuslampiran() {
        $audit_id    = (int) $this->input->post('audit_id');
        $lampiran_id = (int) $this->input->post('lampiran_id');

        $kunci = $this->_kunci($audit_id);
        if ($kunci !== TRUE) {
            $this->_json($kunci);
            return;
        }

        $row = $this->lampiran->satu($lampiran_id);
        if (!$row || (int) $row['audit_id'] !== $audit_id) {
            $this->_json(array('status' => false, 'pesan' => 'Lampiran tidak ditemukan pada audit ini.'));
            return;
        }

        $this->lampiran->hapus($lampiran_id);
        $this->_json(array('status' => true, 'pesan' => 'Lampiran dihapus.'));
    }

    /**
     * Pastikan audit ini memang milik auditee yang sedang masuk dan masih
     * boleh diubah. Mengembalikan TRUE bila boleh, atau array pesan JSON.
     */
    private function _kunci($audit_id) {
        $audit = $this->mutu->getAuditById($audit_id);
        if (!$audit) {
            return array('status' => false, 'pesan' => 'Audit tidak ditemukan.');
        }

        $users_id = $this->session->userdata('users_id');
        if (isset($audit['auditee_id']) && (int) $audit['auditee_id'] !== (int) $users_id) {
            return array('status' => false, 'pesan' => 'Audit ini bukan milik unit Anda.');
        }

        $status = strtoupper(trim((string) $audit['audit_status']));
        if ($status === 'TERKIRIM' || $status === 'SELESAI') {
            return array(
                'status' => false,
                'pesan'  => 'Hasil evaluasi sudah dikirim, jawaban dan lampiran tidak dapat diubah lagi.',
            );
        }

        return TRUE;
    }

    /** Balas JSON. */
    private function _json($data) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($data);
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
