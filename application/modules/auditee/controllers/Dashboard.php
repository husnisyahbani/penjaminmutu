<?php

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditee';
        $this->load->js(base_url("assets/app/auditee/daftaraudit.js?v=2.2"));
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
        // Pembantu teks butir (lingkup_bersihkan) untuk pesan validasi.
        $this->load->helper('lingkup');

        // Jawaban wajib + lampiran opsional per butir lingkup (halaman detail audit).
        $this->load->js(base_url("assets/app/auditee/jawaban-lingkup.js?v=3.1"));

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
            /* Filter periode: bawaan = periode aktif. Bila tidak ada periode
               aktif, seluruh data ditampilkan (periode_id = NULL). Tabel dan
               kartu statistik mengikuti periode yang dipilih. */
            $this->data['list_periode'] = $this->periode->getAll();
            $this->data['periode_aktif'] = $this->periode->aktifId();
            $filter = $this->data['periode_aktif'];

            $this->data['totalterkirim'] = $this->mutu->totalTerkirim($filter);
            $this->data['totalproses'] = $this->mutu->totalProses($filter);
            $this->data['totalselesai'] = $this->mutu->totalSelesai($filter);
            $this->data['totaldraft'] = $this->mutu->totalDraft($filter);
            $this->data['listauditor'] = $this->akun->getAllAuditor();
            $this->data['listauditee'] = $this->akun->getAllAuditee();
            /* Pilihan formulir mengikuti periode aktif (formulir lama tanpa periode
               tetap ikut tampil). */
            $this->data['formulir'] = $this->formulir->getAllFormulir($this->periode->aktifId());
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
    }

    /**
     * Jumlah audit per status untuk periode terpilih - dipakai kartu
     * statistik agar ikut berubah saat filter periode diganti.
     */
    public function statistik() {
        $this->output->set_content_type('application/json');

        $periode_id = $this->input->post('periode_id');

        $query = array(
            'status' => TRUE,
            'draft' => (int) $this->mutu->totalDraft($periode_id),
            'terkirim' => (int) $this->mutu->totalTerkirim($periode_id),
            'proses' => (int) $this->mutu->totalProses($periode_id),
            'selesai' => (int) $this->mutu->totalSelesai($periode_id),
        );
        echo json_encode($query);
    }

    public function detail($id) {
        if(isset($id)){
            $this->data['content'] = 'dashboard/detail';
            $this->data['title'] = 'Daftar Audit';
            $this->data['audit_id'] = $id;
            $this->data['dashboard'] = 'active';
            $this->data['result'] = $this->mutu->getAuditById($id);
            /* Daftar pertanyaan yang dijawab auditee = butir lingkup pada
               tabel mutu_lingkup, lengkap dengan jawaban, lampiran,
               penilaian auditor, dan koreksi auditee. */
            $this->data['topik'] = $this->auditjawab->petaLingkup($id);
            /* Tabel mutu_lingkup belum diimpor -> bukan salah data, tampilkan
               petunjuk pada halaman. */
            $this->data['lingkup_siap'] = $this->auditjawab->lingkupSiap();

            /* Jawaban hanya dapat diisi/diubah selama audit masih DRAFT.
               Status lain (TERKIRIM / PROSES / SELESAI) dikunci. */
            $status = strtoupper(trim((string) $this->data['result']['audit_status']));
            $this->data['status_audit'] = $status;
            $this->data['sudah_terkirim'] = ($status !== 'DRAFT');
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

        // Jawaban hanya boleh ditulis selama audit masih draft.
        $kunci = $this->_kunci($data['audit_id']);
        if ($kunci !== TRUE) {
            $this->_json($kunci);
            return;
        }

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

        // Hanya audit berstatus draft yang boleh dikirim.
        $status = strtoupper(trim((string) $audit['audit_status']));
        if ($status !== 'DRAFT') {
            $this->_json(array(
                'status' => false,
                'pesan'  => 'Hasil evaluasi hanya dapat dikirim saat audit masih berstatus draft.',
            ));
            return;
        }

        // Seluruh butir lingkup wajib dijawab sebelum hasil dikirim ke auditor.
        $kurang = $this->auditjawab->lingkupBelumDijawab($id);
        if (!empty($kurang)) {
            $contoh = array();
            foreach (array_slice($kurang, 0, 3) as $k) {
                $contoh[] = lingkup_bersihkan($k['lingkup_isi']);
            }
            $satuan = 'pertanyaan';
            foreach ($kurang as $k) {
                if ((int) $k['lingkup_id'] > 0) {
                    $satuan = 'butir lingkup';
                    break;
                }
            }
            $pesan = 'Masih ada ' . count($kurang) . ' ' . $satuan . ' yang belum dijawab.';
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

    /**
     * Id pertanyaan yang dikirim halaman detail audit: dtform_id. Pemanggil
     * lama (butir tilik / lingkup) dipetakan ke pertanyaannya.
     */
    private function _dtformId() {
        $dtform_id = (int) $this->input->post('dtform_id');
        if ($dtform_id > 0) {
            return $dtform_id;
        }

        $dtjwb_id = (int) $this->input->post('dtjwb_id');
        if ($dtjwb_id > 0) {
            $butir = $this->auditjawab->tilikAudit((int) $this->input->post('audit_id'), $dtjwb_id);
            return $butir ? (int) $butir['dtform_id'] : 0;
        }

        return 0;
    }

    /**
     * Simpan jawaban auditee untuk satu BUTIR LINGKUP (halaman detail audit).
     *
     * Daftar pertanyaan pada halaman detail berasal dari mutu_lingkup, jadi
     * tiap butir lingkup dijawab sendiri-sendiri. Bila butir lingkup tidak
     * dikirim (pertanyaan tanpa butir lingkup / data lama), jawaban disimpan
     * pada baris pertanyaan seperti sebelumnya.
     */
    public function jawablingkup() {
        $audit_id   = (int) $this->input->post('audit_id');
        $lingkup_id = (int) $this->input->post('lingkup_id');
        $dtform_id  = $this->_dtformId();
        $teks       = $this->input->post('jwb_jawaban');

        if (empty($audit_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Audit tidak diketahui.'));
            return;
        }

        $kunci = $this->_kunci($audit_id);
        if ($kunci !== TRUE) {
            $this->_json($kunci);
            return;
        }

        // Butir lingkup: jawaban tersimpan per butir (auditjawab.lingkup_id).
        if ($lingkup_id > 0) {
            $hasil = $this->auditjawab->simpanJawabanButir($audit_id, $lingkup_id, $teks);
            $this->_json($hasil);
            return;
        }

        // Cadangan: pertanyaan yang belum punya butir lingkup.
        if (empty($dtform_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Pertanyaan tidak diketahui.'));
            return;
        }

        $hasil = $this->auditjawab->simpanJawabanPertanyaan($audit_id, $dtform_id, $teks);
        $this->_json($hasil);
    }

    /**
     * Unggah lampiran satu lingkup. Berkas boleh lebih dari satu
     * (input name="lampiran[]").
     */
    public function unggahlampiran() {
        $audit_id   = (int) $this->input->post('audit_id');
        $lingkup_id = (int) $this->input->post('lingkup_id');
        $dtform_id  = $this->_dtformId();

        if (empty($audit_id)) {
            $this->_json(array('status' => false, 'pesan' => 'Audit tidak diketahui.'));
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

        /* Lampiran menempel pada jawaban: baris butir lingkup bila ada,
           kalau tidak baris pertanyaan (data lama). Kolom yang dipakai
           mengikuti struktur tabel lampiran (jwb_id atau lingkup_id). */
        $ekstra = array();

        if ($lingkup_id > 0) {
            $jwb_id = $this->auditjawab->pastikanButir($audit_id, $lingkup_id);
            if (!$jwb_id) {
                $this->_json(array('status' => false, 'pesan' => 'Butir lingkup tidak ditemukan pada audit ini.'));
                return;
            }
            $kolom_lampiran = $this->lampiran->kolomJawaban();
            $id_lampiran    = ($kolom_lampiran === 'jwb_id') ? $jwb_id : $lingkup_id;
            if ($kolom_lampiran === 'jwb_id') {
                // Penanda butir ikut tersimpan supaya lampiran mudah dibaca
                // kembali per butir lingkup.
                $ekstra['lingkup_id'] = $lingkup_id;
            }
        } else {
            if (empty($dtform_id)) {
                $this->_json(array('status' => false, 'pesan' => 'Pertanyaan tidak diketahui.'));
                return;
            }
            $jwb_id = $this->auditjawab->pastikanPertanyaan($audit_id, $dtform_id);
            if (!$jwb_id) {
                $this->_json(array('status' => false, 'pesan' => 'Pertanyaan tidak ditemukan pada audit ini.'));
                return;
            }
            $kolom_lampiran = $this->lampiran->kolomJawaban();
            $id_lampiran    = ($kolom_lampiran === 'jwb_id') ? $jwb_id : $dtform_id;
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
                $id   = $this->lampiran->tambah($audit_id, $id_lampiran, $info, $kolom_lampiran, $ekstra);

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

        /* Jawaban & lampiran hanya dapat diubah selama audit masih DRAFT.
           Setelah dikirim, dikunci sampai penilaian auditor selesai. */
        $status = strtoupper(trim((string) $audit['audit_status']));
        if ($status !== 'DRAFT') {
            $pesan = array(
                'TERKIRIM' => 'Hasil evaluasi sudah dikirim ke auditor, jawaban dan lampiran tidak dapat diubah lagi.',
                'PROSES'   => 'Audit sedang dinilai auditor, jawaban dan lampiran tidak dapat diubah lagi.',
                'SELESAI'  => 'Audit sudah selesai, jawaban dan lampiran tidak dapat diubah lagi.',
            );
            return array(
                'status' => false,
                'pesan'  => isset($pesan[$status])
                    ? $pesan[$status]
                    : 'Jawaban dan lampiran hanya dapat diubah saat audit masih berstatus draft.',
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


        /* Filter periode (bawaan periode aktif dikirim oleh daftaraudit.js;
           kosong = semua periode). */
        $periode_id = $this->input->post('periode_id');

        $list = $this->mutu->get_datatables($post['length'], $post['start'], $post['search'], $post['order'], $periode_id);
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
            "recordsTotal" => $this->mutu->count_all($periode_id),
            "recordsFiltered" => $this->mutu->count_filtered($post['search'], $post['order'], $periode_id),
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
