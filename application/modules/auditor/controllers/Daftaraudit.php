<?php

class Daftaraudit extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/daftaraudit.js?v=2.4"));
        // Informasi tombol aksi saat hover (lihat assets/app/tabel-aksi.css)
        $this->load->js(base_url("assets/app/tabel-aksi.js?v=1.0"));
        // Tampilan topik & activity pada halaman detail audit.
        $this->load->js(base_url("assets/app/topik-aktivitas.js?v=1.0"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('AuditJawabDetailModel', 'auditjawabdetail');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');
        $this->load->model('PeriodeModel', 'periode');

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
            /* Filter periode: bawaan = periode aktif. Bila tidak ada periode
               aktif, seluruh data ditampilkan. Tabel dan kartu statistik
               mengikuti periode yang dipilih.
               Bila tabel periode belum disiapkan (kolom mutu_audit.periode_id
               belum ada), kotak saring tidak ditampilkan. */
            $this->data['list_periode'] = $this->periode->siap() ? $this->periode->getAll() : array();
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
     * statistik agar ikut berubah saat saringan periode diganti.
     */
    public function statistik() {
        $this->output->set_content_type('application/json');

        $periode_id = $this->input->post('periode_id');

        echo json_encode(array(
            'status'   => TRUE,
            'draft'    => (int) $this->mutu->totalDraft($periode_id),
            'terkirim' => (int) $this->mutu->totalTerkirim($periode_id),
            'proses'   => (int) $this->mutu->totalProses($periode_id),
            'selesai'  => (int) $this->mutu->totalSelesai($periode_id),
        ));
    }

    /**
     * Unduh hasil audit: DAFTAR TILIK AMI (.xlsx) berisi SELURUH daftar
     * tilik audit ini, tanpa saringan pertanyaan.
     *
     * Bentuknya sama dengan tombol "Unduh Excel" pada halaman delik
     * (application/libraries/Tilikexcel.php) - header identitas dokumen,
     * informasi pelaksanaan, tabel bertingkat (No, Tujuan, Referensi,
     * Lingkup Pertanyaan, Hasil observasi, Temuan S/OB/TS, Catatan) dan
     * footer kepemilikan dokumen - tetapi isinya tidak dibatasi pada satu
     * pertanyaan: seluruh pertanyaan pada formulir audit ini ditampilkan
     * berurutan dan kolom Tujuan menunjukkan pertanyaan asal tiap butir.
     *
     * Satu lembar per audit. Dipanggil tombol "Unduh hasil audit" pada
     * daftar audit (POST ids[], lihat assets/app/<modul>/daftaraudit.js).
     */
    public function download() {
        $ids = $this->input->post('ids');
        if (!$ids || count($ids) == 0) {
            show_error('Data audit tidak ditemukan');
        }

        $this->load->library('tilikexcel');
        $this->load->model('TimauditModel', 'timaudit');

        $excel = new PHPExcel();
        $index = 0;
        $unit  = array();

        foreach ($ids as $id) {
            $audit = $this->mutu->getAuditById($id);
            if (!$audit) {
                continue;
            }

            $unit[] = isset($audit['unit']) ? $audit['unit'] : '';
            $nama_lembar = ($index === 0)
                ? 'Daftar Tilik AMI'
                : 'Daftar Tilik AMI (' . ($index + 1) . ')';

            $this->tilikexcel->lembar($excel, $this->_tilikAudit($audit), $nama_lembar, $index);
            $index++;
        }

        if ($index === 0) {
            show_error('Data audit tidak ditemukan');
        }

        /* Nama berkas: satu audit => "Daftar Tilik AMI - <unit>". */
        $nama = 'Daftar Tilik AMI';
        if ($index === 1 && $unit[0] !== '') {
            $nama .= ' - ' . $unit[0];
        }

        $this->tilikexcel->kirim($excel, $nama);
    }

    /**
     * Kumpulkan seluruh butir tilik satu audit (SEMUA pertanyaan) beserta
     * informasi pelaksanaannya, siap dibentuk menjadi lembar Daftar Tilik AMI.
     *
     * @param  array $audit baris mutu_audit (MutuauditModel::getAuditById)
     * @return array kunci: tanggal_audit, unit, auditor, auditee, baris
     */
    private function _tilikAudit($audit) {
        $audit_id = (int) $audit['audit_id'];
        $daftar   = array();

        $dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
        foreach ($dtform as $row) {
            $jwb    = $this->auditjawab->getAuditJawabFix($audit_id, $row['dtform_id']);
            $tujuan = isset($jwb['jwb_tujuan']) ? $jwb['jwb_tujuan'] : '';

            /* Butir tilik pertanyaan ini: butir lingkup (struktur baru)
               berikut sisa baris lama. */
            $detail = $this->auditjawab->barisTilik($audit_id, $row['dtform_id']);
            $daftar = array_merge($daftar, $this->tilikexcel->susun($detail, $tujuan));
        }

        /* Tanggal Audit: tanggal butir tilik dibuat. Bila kolomnya belum
           ikut pada kueri ini, pakai tanggal pembaruan audit. */
        $tanggal = $this->tilikexcel->tanggalAudit($daftar);
        if ($tanggal === '' && !empty($audit['audit_update'])) {
            $tanggal = date('d/m/Y', strtotime($audit['audit_update']));
        }

        $tim = $this->timaudit->daftar($audit_id);

        return array(
            'tanggal_audit' => $tanggal,
            'unit'          => isset($audit['unit']) ? $audit['unit'] : '',
            'auditor'       => $this->timaudit->teks($tim, isset($audit['auditor']) ? $audit['auditor'] : ''),
            'auditee'       => isset($audit['auditee']) ? $audit['auditee'] : '',
            'baris'         => $daftar,
        );
    }


    public function detail($id) {
        if(isset($id)){
            $this->data['content'] = 'daftaraudit/detail';
            $this->data['title'] = 'Daftar Audit';
            $this->data['audit_id'] = $id;
            $this->data['result'] = $this->mutu->getAuditById($id);
            /* Butir lingkup (mutu_lingkup) beserta jawaban auditee per butir,
               lampiran, dan hasil penilaian yang sudah tersimpan. */
            $this->data['topik'] = $this->auditjawab->petaLingkup($id);
            /* Catatan auditor per butir lingkup: kelebihan & peluang untuk
               peningkatan (tabel mutu_lingkup_nilai). Bila tabelnya belum
               diimpor, bagian itu disembunyikan dan halaman menampilkan
               keterangan, persis seperti perlakuan fitur lampiran. */
            $this->load->model('LingkupnilaiModel', 'lingkupnilai');
            $this->data['catatan_siap'] = $this->lingkupnilai->siap();
            $this->data['js'] = $this->load->get_js_files();
            $this->data['audit'] = 'active';
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
        }
    }

    

    /**
     * Simpan catatan auditor untuk satu butir lingkup: "Kelebihan" dan
     * "Peluang untuk peningkatan" (halaman detail audit).
     *
     * Data tersimpan pada mutu_lingkup_nilai, dipisahkan per audit dan per
     * butir lingkup, sehingga tidak bergantung pada baris tilik.
     */
    public function simpancatatan() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $audit_id   = (int) $this->input->post('audit_id');
        $lingkup_id = (int) $this->input->post('lingkup_id');
        $kelebihan  = trim(strip_tags((string) $this->input->post('kelebihan')));
        $peluang    = trim(strip_tags((string) $this->input->post('peluang')));

        $this->load->model('LingkupnilaiModel', 'lingkupnilai');

        if (!$this->lingkupnilai->siap()) {
            echo json_encode(array(
                'status' => FALSE,
                'pesan'  => 'Fitur catatan belum siap. Impor database/kelebihan_peluang.sql terlebih dahulu.',
            ));
            return;
        }

        if ($audit_id < 1 || $lingkup_id < 1) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Butir lingkup tidak dikenal.'));
            return;
        }

        /* Penjagaan: butir harus termasuk pertanyaan pada audit ini. */
        if (!$this->auditjawab->lingkupMilikAudit($audit_id, $lingkup_id)) {
            echo json_encode(array(
                'status' => FALSE,
                'pesan'  => 'Butir lingkup tidak ditemukan pada audit ini.',
            ));
            return;
        }

        $status = $this->lingkupnilai->simpan($audit_id, $lingkup_id, $kelebihan, $peluang);

        echo json_encode(array(
            'status' => $status,
            'pesan'  => $status ? 'Catatan tersimpan.' : 'Catatan gagal disimpan.',
        ));
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

    public function proses() {
        $data = array();
        $data['audit_id'] = $this->input->post('id');
        $data['audit_status'] = "PROSES";
        $this->mutu->edit($data);
    }

    /**
     * Selesaikan penilaian.
     *
     * Penilaian hanya boleh diselesaikan bila tujuan setiap pertanyaan sudah
     * diisi dan pilihan temuan setiap butir daftar tilik (delik) sudah
     * ditentukan - permintaan PPM. Bila belum lengkap, status audit tidak
     * diubah dan rincian kekurangannya dikirim balik supaya ditampilkan
     * kepada auditor (lihat assets/app/auditor/daftaraudit.js).
     */
    public function selesai() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $audit_id = (int) $this->input->post('id');

        if ($audit_id < 1) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Audit tidak dikenal.'));
            return;
        }

        $cek = $this->auditjawab->kelengkapan($audit_id);

        if (!$cek) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Audit tidak ditemukan.'));
            return;
        }

        if (!$cek['lengkap']) {
            echo json_encode(array(
                'status' => FALSE,
                'pesan'  => $this->_pesanBelumLengkap($cek),
            ));
            return;
        }

        $data = array();
        $data['audit_id'] = $audit_id;
        $data['audit_status'] = "SELESAI";
        $status = $this->mutu->edit($data);

        echo json_encode(array(
            'status' => (bool) $status,
            'pesan'  => $status
                ? 'Formulir telah selesai diproses.'
                : 'Gagal menyimpan perubahan ke database.',
        ));
    }

    /**
     * Susun pesan kekurangan penilaian: berapa tujuan dan berapa butir
     * daftar tilik yang belum terisi, disertai beberapa contoh supaya
     * auditor tahu bagian mana yang harus dilengkapi.
     *
     * @param  array $cek keluaran AuditjawabModel::kelengkapan()
     * @return string
     */
    private function _pesanBelumLengkap($cek) {
        $bagian = array();

        if (!empty($cek['tujuan_kosong'])) {
            $bagian[] = count($cek['tujuan_kosong']) . ' dari ' . $cek['pertanyaan_total']
                . ' tujuan pertanyaan belum diisi';
        }

        if (!empty($cek['delik_kosong'])) {
            $bagian[] = count($cek['delik_kosong']) . ' dari ' . $cek['delik_total']
                . ' butir daftar tilik belum ditentukan temuannya';
        }

        $pesan = 'Penilaian belum bisa diselesaikan: ' . implode(' dan ', $bagian) . '.';

        $contoh = array();
        foreach (array_slice($cek['tujuan_kosong'], 0, 2) as $t) {
            $contoh[] = '- Tujuan belum diisi pada pertanyaan: ' . $t;
        }
        foreach (array_slice($cek['delik_kosong'], 0, 2) as $d) {
            $contoh[] = '- Temuan belum dipilih pada butir: ' . $d;
        }

        if (!empty($contoh)) {
            $pesan .= "\n" . implode("\n", $contoh);
        }

        /* json_encode gagal bila menjumpai karakter bukan UTF-8 (mis. data
           impor lama). Balasan yang kosong akan dibaca penampil sebagai
           penilaian berhasil diselesaikan, jadi karakter itu dibuang dulu. */
        if (function_exists('mb_convert_encoding')) {
            $pesan = mb_convert_encoding($pesan, 'UTF-8', 'UTF-8');
        }

        return $pesan;
    }

    public function hapus() {
        $id = $this->input->post('id');
        $this->mutu->hapus($id);
    }

    public function kembali() {
        $id = $this->input->post('id');
        $catatan = $this->input->post('catatan');

        if (empty($id)) {
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'pesan' => 'ID audit tidak valid.'));
            return;
        }

        // Pastikan kolom audit_catatan ada di tabel mutu_audit
        if (!$this->db->field_exists('audit_catatan', 'mutu_audit')) {
            $this->load->dbforge();
            $this->dbforge->add_column('mutu_audit', array(
                'audit_catatan' => array('type' => 'TEXT', 'null' => TRUE, 'after' => 'audit_status'),
            ));
        }

        $data = array();
        $data['audit_status'] = 'DRAFT'; 
        $data['audit_id'] = $id;
        if ($this->db->field_exists('audit_catatan', 'mutu_audit')) {
            $data['audit_catatan'] = $catatan !== null ? trim($catatan) : '';
        }

        $status = $this->mutu->edit($data);
        $query = array("status" => (bool) $status);
        if (!$status) {
            $query['pesan'] = 'Gagal menyimpan perubahan ke database.';
        }

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function listmutu() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');

        /* Saringan periode (bawaan periode aktif dikirim daftaraudit.js;
           kosong = semua periode). */
        $periode_id = $this->input->post('periode_id');

        $list = $this->mutu->get_datatables($post['length'], $post['start'], $post['search'], $post['order'], $periode_id);

        /* Tim auditor (ketua + anggota) untuk audit pada halaman ini. */
        $this->load->model('TimauditModel', 'timaudit');
        $id_halaman = array();
        foreach ($list as $field) {
            $id_halaman[] = (int) $field->audit_id;
        }
        $peta_tim = $this->timaudit->peta($id_halaman);

        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->form_nama;
            /* Kolom Auditor: seluruh nama tim, ketua ditandai "(Ketua)". */
            $baris_tim = isset($peta_tim[(int) $field->audit_id]) ? $peta_tim[(int) $field->audit_id] : array();
            /* Satu nama per baris: dipisahkan baris baru lalu diubah
               menjadi <br> setelah karakter HTML-nya disaring. */
            $nama_tim = $this->timaudit->teks($baris_tim, $field->auditor, "\n");
            $row[] = '<div class="tim-teks">' . nl2br(html_escape($nama_tim)) . '</div>';
            $row[] = $field->auditee;
            $row[] = $field->unit; 

            /* ================= TOMBOL AKSI =================
               Pola seragam: btn btn-sm btn-icon btn-<warna> + ikon saja
               (tanpa teks, sesuai gaya tabel admin). Tombol dibungkus
               .tabel-aksi supaya selalu satu baris dan jaraknya
               konsisten.

               Keterangan tiap tombol muncul saat kursor diarahkan (hover)
               atau saat tombol mendapat fokus keyboard, memakai atribut
               data-info yang diolah assets/app/tabel-aksi.js (delegasi di
               document, jadi baris baru dari DataTables ikut terjangkau).
               Tooltip bawaan tema tidak dipakai karena $.fn.tooltip pada
               tema ini diambil alih jQuery UI. */
            $btn_detail = '<button type="button" class="detail btn btn-sm btn-icon btn-primary" '
                . 'data-info="Lihat rincian penilaian" '
                . 'aria-label="Detail" id="' . $field->audit_id . '">'
                . '<i class="icon md-book" aria-hidden="true"></i></button>';

            $btn_unduh = '<button type="button" class="download btn btn-sm btn-icon btn-success" '
                . 'data-info="Unduh hasil audit" '
                . 'aria-label="Unduh" id="' . $field->audit_id . '">'
                . '<i class="icon md-download" aria-hidden="true"></i></button>';

            $btn_proses = '<button type="button" class="proses btn btn-sm btn-icon btn-warning" '
                . 'data-info="Mulai proses penilaian" '
                . 'aria-label="Proses" id="' . $field->audit_id . '">'
                . '<i class="icon md-play" aria-hidden="true"></i></button>';

            $btn_kembali = '<button type="button" class="kembali btn btn-sm btn-icon btn-danger" '
                . 'data-info="Kembalikan ke auditee" '
                . 'aria-label="Kembalikan" id="' . $field->audit_id . '">'
                . '<i class="icon md-undo" aria-hidden="true"></i></button>';

            $btn_selesai = '<button type="button" class="selesai btn btn-sm btn-icon btn-success" '
                . 'data-info="Selesaikan penilaian" '
                . 'aria-label="Selesai" id="' . $field->audit_id . '">'
                . '<i class="icon md-check" aria-hidden="true"></i></button>';

            $tunggu = '<span class="badge badge-default">Menunggu proses</span>';

            // ---- kolom Aksi: seluruh tombol, seragam, sesuai status ----
            if ($field->audit_status == "PROSES") {
                $aksi = $btn_detail . $btn_kembali . $btn_selesai;
            } else if ($field->audit_status == "SELESAI") {
                $aksi = $btn_detail . $btn_unduh;
            } else if ($field->audit_status == "TERKIRIM") {
                $aksi = $btn_proses;
            } else {
                $aksi = $tunggu; // DRAFT / status lain: belum ada aksi
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

    function truncate_words(string $text, int $maxWords = 200, string $ellipsis = '...'): string {
    // bersihkan whitespace berlebih dan hilangkan tag HTML jika perlu
    $clean = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
    if ($clean === '') return $clean;

    $words = preg_split('/\s+/', $clean);
    if (count($words) <= $maxWords) {
        return $clean;
    }

    $truncated = array_slice($words, 0, $maxWords);
    return implode(' ', $truncated) . $ellipsis;
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
            //$jawaban = $this->truncate_words($field->jwb_jawaban);
           // $row[] = $field->jwb_jawaban;
           if(isset($field->jwb_jawaban)){
                $row[] = ' <button type="button" class="delik btn btn-warning btn-xs waves-effect waves-classic" dtform_id=' . $field->dtform_id.' audit_id=' . $field->audit_id.'><i class="icon md-edit" aria-hidden="true"></i>Lihat Hasil dan Delik</button>';
           }else{
                $row[] = '';
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
