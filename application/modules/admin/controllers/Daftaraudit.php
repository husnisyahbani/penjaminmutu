<?php

class Daftaraudit extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/daftaraudit.js?v=2.5"));
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
        // Pembantu teks butir (lingkup_bersihkan) untuk halaman detail audit.
        $this->load->helper('lingkup');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function tes(){
        $audit = $this->mutu->getAuditById("43");
        $dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
        foreach($dtform as $value){
            $jwb = $this->auditjawab->getAuditJawabFix($audit['audit_id'],$value['dtform_id']);
            $detail = $this->auditjawabdetail->getAuditJawabDetail($jwb['jwb_id']);
            echo "<pre>";
            print_r($jwb);
            echo "</pre>";  
        } 
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
            'kode'          => isset($audit['form_kode']) ? $audit['form_kode'] : '',
            'tanggal_audit' => $tanggal,
            'unit'          => isset($audit['unit']) ? $audit['unit'] : '',
            'auditor'       => $this->timaudit->teks($tim, isset($audit['auditor']) ? $audit['auditor'] : ''),
            'auditee'       => isset($audit['auditee']) ? $audit['auditee'] : '',
            'baris'         => $daftar,
        );
    }

    /**
     * Unduh LAPORAN AUDIT MUTU INTERNAL (AMI) (.xlsx) satu audit.
     *
     * Dipanggil tombol unduh pada kolom Aksi daftar audit (audit berstatus
     * SELESAI). Bila kesimpulan audit (Bagian VII) belum pernah diisi,
     * halaman menampilkan modal untuk menjawabnya terlebih dahulu
     * (lihat kesimpulan() dan simpankesimpulan()).
     */
    public function laporan($id) {
        $audit = $this->mutu->getAuditById((int) $id);
        if (!$this->_bolehLaporan($audit)) {
            show_error('Audit tidak ditemukan atau tidak dapat diunduh.', 403);
            return;
        }
        $this->load->model('KesimpulanModel', 'kesimpulan');
        if (!$this->kesimpulan->siap() || !$this->kesimpulan->sudahDiisi($id)) {
            show_error('Kesimpulan audit harus disimpan sebelum mengunduh.', 400);
            return;
        }

        $this->load->library('laporanami');
        $this->load->model('TimauditModel', 'timaudit');

        $excel = $this->laporanami->buat($this->_laporanAudit($audit));

        $nama = 'Laporan AMI';
        if (!empty($audit['unit'])) {
            $nama .= ' - ' . $audit['unit'];
        }

        $this->laporanami->kirim($excel, $nama);
    }

    /**
     * Status kesimpulan audit (Bagian VII Laporan AMI) untuk modal.
     *
     * Mengembalikan JSON: sudah = kesimpulan sudah terisi lengkap,
     * kesimpulan = jawaban tersimpan (bila ada).
     */
    public function kesimpulan($id) {
        header('Content-Type: application/json');

        $this->load->model('KesimpulanModel', 'kesimpulan');
        if (!$this->_bolehLaporan($this->mutu->getAuditById((int) $id))) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Audit tidak ditemukan atau tidak dapat diunduh.'));
            return;
        }
        if (!$this->kesimpulan->siap()) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Tabel kesimpulan belum tersedia. Minta PPM mengimpor database/kesimpulan_audit.sql.'));
            return;
        }

        echo json_encode(array(
            'status'     => TRUE,
            'sudah'      => $this->kesimpulan->sudahDiisi($id),
            'kesimpulan' => $this->kesimpulan->ambil($id),
        ));
    }

    /**
     * Simpan jawaban kesimpulan audit dari modal (POST).
     */
    public function simpankesimpulan() {
        header('Content-Type: application/json');

        $audit_id = (int) $this->input->post('audit_id');
        $k1 = $this->input->post('kesimpulan1');
        $k2 = $this->input->post('kesimpulan2');
        $k3 = $this->input->post('kesimpulan3');

        $boleh1 = array('Ya', 'Tidak', 'Lainnya');

        if ($audit_id < 1 || !in_array($k1, $boleh1, TRUE) || !in_array($k2, $boleh1, TRUE)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Jawaban kesimpulan belum lengkap.'));
            return;
        }

        if (!$this->_bolehLaporan($this->mutu->getAuditById($audit_id))) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Audit tidak ditemukan atau tidak dapat diunduh.'));
            return;
        }
        if (($k1 === 'Lainnya' && !trim(strip_tags((string) $this->input->post('kesimpulan1_lainnya')))) ||
            ($k2 === 'Lainnya' && !trim(strip_tags((string) $this->input->post('kesimpulan2_lainnya'))))) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Harap isi keterangan untuk pilihan Lainnya.'));
            return;
        }

        $data = array(
            'kesimpulan1'         => $k1,
            'kesimpulan1_lainnya' => $k1 === 'Lainnya' ? trim(strip_tags((string) $this->input->post('kesimpulan1_lainnya'))) : NULL,
            'kesimpulan2'         => $k2,
            'kesimpulan2_lainnya' => $k2 === 'Lainnya' ? trim(strip_tags((string) $this->input->post('kesimpulan2_lainnya'))) : NULL,
            'kesimpulan3'         => $k3,
        );

        $this->load->model('KesimpulanModel', 'kesimpulan');
        if (!$this->kesimpulan->siap()) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Tabel kesimpulan belum tersedia. Minta PPM mengimpor database/kesimpulan_audit.sql.'));
            return;
        }
        $status = $this->kesimpulan->simpan($audit_id, $data);

        echo json_encode(array(
            'status' => $status,
            'pesan'  => $status ? 'Kesimpulan tersimpan.' : 'Kesimpulan gagal disimpan.',
        ));
    }

    /** Batasi laporan pada audit selesai yang dapat diakses pengguna. */
    private function _bolehLaporan($audit) {
        if (!$audit || $audit['audit_status'] !== 'SELESAI') {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Kumpulkan data Laporan AMI satu audit dari data yang sudah ada:
     * identitas pelaksanaan, lingkup, temuan, peluang peningkatan, dan
     * kesimpulan audit.
     *
     * @param  array $audit baris mutu_audit (MutuauditModel::getAuditById)
     * @return array siap untuk Laporanami::buat()
     */
    private function _laporanAudit($audit) {
        $audit_id = (int) $audit['audit_id'];

        $lingkup_teks = array();   // teks butir lingkup (unik, berurutan)
        $peta_lingkup = array();   // lingkup_id => teks butir
        $peta_aspek   = array();   // lingkup_id => dtform_pertanyaan
        $temuan       = array();
        $baris        = array();

        // Peta langsung dari tabel lingkup ke detailform (dtform_pertanyaan)
        if ($this->db->table_exists('lingkup')) {
            $this->db->select('lg.lingkup_id, df.dtform_pertanyaan');
            $this->db->from('lingkup lg');
            $this->db->join('detailform df', 'df.dtform_id = lg.dtform_id', 'inner');
            $this->db->where('df.form_id', (int) $audit['form_id']);
            foreach ($this->db->get()->result_array() as $lg) {
                if (!empty($lg['dtform_pertanyaan'])) {
                    $peta_aspek[(int) $lg['lingkup_id']] = trim(strip_tags((string) $lg['dtform_pertanyaan']));
                }
            }
        }

        $dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
        $tujuan_lain = array();
        $standar_list = array();
        foreach ($dtform as $row) {
            $st = '';
            if (!empty($row['dtform_pertanyaan'])) {
                $st = trim(strip_tags((string) $row['dtform_pertanyaan']));
                if (!in_array($st, $standar_list)) {
                    $standar_list[] = $st;
                }
            }
            if (!empty($row['dtform_id']) && !isset($peta_aspek[(int) $row['dtform_id']])) {
                $peta_aspek[(int) $row['dtform_id']] = $st;
            }

            $jawaban = $this->auditjawab->getAuditJawabFix($audit_id, $row['dtform_id']);
            if (!empty($jawaban['jwb_tujuan'])) {
                $tujuan_lain[] = trim(strip_tags($jawaban['jwb_tujuan']));
            }
            $detail = $this->auditjawab->barisTilik($audit_id, $row['dtform_id']);
            foreach ($detail as $b) {
                $baris[] = $b;

                $teks = isset($b['dtjwb_pertanyaan']) ? trim(strip_tags((string) $b['dtjwb_pertanyaan'])) : '';
                if ($teks !== '') {
                    if (!in_array($teks, $lingkup_teks)) {
                        $lingkup_teks[] = $teks;
                    }
                    if (isset($b['lingkup_id'])) {
                        $peta_lingkup[(int) $b['lingkup_id']] = $teks;
                        if (!isset($peta_aspek[(int) $b['lingkup_id']]) && $st !== '') {
                            $peta_aspek[(int) $b['lingkup_id']] = $st;
                        }
                    }
                }

                $kategori = isset($b['dtjwb_temuan']) ? strtoupper(trim((string) $b['dtjwb_temuan'])) : '';
                if ($kategori !== '' && $kategori !== 'S') {
                    $referensi  = isset($b['dtjwb_referensi']) && trim((string) $b['dtjwb_referensi']) !== ''
                        ? trim(strip_tags((string) $b['dtjwb_referensi']))
                        : '';
                    $pertanyaan = $st !== '' ? $st : (isset($b['lingkup_id']) && isset($peta_aspek[(int) $b['lingkup_id']]) ? $peta_aspek[(int) $b['lingkup_id']] : '');

                    if ($pertanyaan !== '' && $referensi !== '') {
                        $indikator = $pertanyaan . "\n" . $referensi;
                    } elseif ($pertanyaan !== '') {
                        $indikator = $pertanyaan;
                    } elseif ($referensi !== '') {
                        $indikator = $referensi;
                    } else {
                        $indikator = $teks;
                    }

                    $temuan[] = array(
                        'kategori'   => $kategori,
                        'indikator'  => $indikator,
                        'pernyataan' => isset($b['dtjwb_hasil']) ? trim(strip_tags((string) $b['dtjwb_hasil'])) : '',
                    );
                }
            }
        }

        /* Tanggal Audit: tanggal butir tilik dibuat (dtjwb_create); bila
           kolomnya belum ikut pada kueri ini, pakai tanggal pembaruan audit. */
        $tanggal = '';
        $paling_awal = 0;
        foreach ($baris as $b) {
            if (empty($b['dtjwb_create'])) {
                continue;
            }
            $waktu = strtotime($b['dtjwb_create']);
            if ($waktu && ($paling_awal === 0 || $waktu < $paling_awal)) {
                $paling_awal = $waktu;
            }
        }
        if ($paling_awal) {
            $tanggal = date('d/m/Y', $paling_awal);
        }
        if ($tanggal === '' && !empty($audit['audit_update'])) {
            $tanggal = date('d/m/Y', strtotime($audit['audit_update']));
        }

        /* Tanggal butir paling awal dari data audit yang tersimpan. */
        if ($this->db->field_exists('dtjwb_create', 'auditjawabdetail')) {
            $this->db->select_min('dtjwb_create', 'tanggal');
            $this->db->from('auditjawabdetail d');
            $this->db->join('auditjawab j', 'j.jwb_id = d.jwb_id');
            $this->db->where('j.audit_id', $audit_id);
            $tgl = $this->db->get()->row_array();
            if (!empty($tgl['tanggal']) && strtotime($tgl['tanggal'])) {
                $tanggal = date('d/m/Y', strtotime($tgl['tanggal']));
            }
        }

        /* Anggota auditor: anggota tim selain ketua. */
        $tim     = $this->timaudit->daftar($audit_id);
        $anggota = array();
        foreach ($tim as $t) {
            if (isset($t['tim_peran']) && $t['tim_peran'] === 'KETUA') {
                continue;
            }
            if (!empty($t['nama'])) {
                $anggota[] = $t['nama'];
            }
        }

        /* Peluang peningkatan: catatan auditor per butir lingkup. */
        $peluang = array();
        $this->load->model('LingkupnilaiModel', 'lingkupnilai');
        if ($this->lingkupnilai->siap()) {
            foreach ($this->lingkupnilai->peta($audit_id) as $lid => $n) {
                $kelebihan = isset($n['kelebihan']) ? trim(strip_tags((string) $n['kelebihan'])) : '';
                $peluang_teks = isset($n['peluang']) ? trim(strip_tags((string) $n['peluang'])) : '';
                if ($kelebihan === '' && $peluang_teks === '') {
                    continue;
                }
                $aspek = isset($peta_aspek[(int) $lid]) ? $peta_aspek[(int) $lid] : (isset($peta_lingkup[(int) $lid]) ? $peta_lingkup[(int) $lid] : '');
                $peluang[] = array(
                    'aspek'     => $aspek,
                    'kelebihan' => $kelebihan,
                    'peluang'   => $peluang_teks,
                );
            }
        }

        /* Kesimpulan audit (Bagian VII). */
        $this->load->model('KesimpulanModel', 'kesimpulan');
        $kesimpulan = $this->kesimpulan->ambil($audit_id);
        if (!$kesimpulan) {
            $kesimpulan = array();
        }

        return array(
            'unit'          => isset($audit['unit']) ? $audit['unit'] : '',
            'alamat'        => !empty($audit['alamat']) ? $audit['alamat'] : 'Jl. Demang Lebar Daun Pakjo Palembang',
            'kabag'         => isset($audit['auditee']) ? $audit['auditee'] : '',
            'tanggal_audit' => $tanggal,
            'ketua_auditor' => isset($audit['auditor']) ? $audit['auditor'] : '',
            'pt'            => 'STIK Siti Khadijah',
            'anggota'       => $anggota,
            'tujuan'        => range(0, 4),
            'tujuan_lain'   => implode("\n", array_unique($tujuan_lain)),
            'lingkup'       => !empty($standar_list) ? $standar_list : $lingkup_teks,
            'temuan'        => $temuan,
            'peluang'       => $peluang,
            'kesimpulan'    => $kesimpulan,
        );
    }






/*
    public function download() {
        
        $ids = $this->input->post('ids');
        if(isset($ids)){
           
            // add a page
            $pdf = new \setasign\Fpdi\Fpdi('L','mm','A4');
            $i = 1;
            foreach($ids as $id){
                $audit = $this->mutu->getAuditById($id);
                // $formulir = $this->formulir->getFormulirById($audit['form_id']);
                //$dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
                //$jwb = $this->auditjawab->getAuditJawab($audit['audit_id'],$dtform[0]['dtform_id']);
                $tanggal = date("d", strtotime($audit['audit_update']));
                $bulan = date("m", strtotime($audit['audit_update']));
                $tahun = date("Y", strtotime($audit['audit_update']));

                $tgl_audit = $tanggal." ".konvbln($bulan)." ".$tahun;
                
                $pdf->AddPage();

                $pdf->SetFont('Arial', 'B', 22);
                $pdf->SetXY(0, 30);
                $pdf->Cell(0,6, $audit['form_nama'],0,1,'C');

                $gambar = FCPATH . 'filedata/logostikfinal.png';

                $pdf->Cell(0,120, '',0,1,'C');
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(0,6, 'PUSAT PENJAMINAN MUTU',0,1,'C');
                $pdf->Cell(0,6, 'SEKOLAH TINGGI ILMU KESEHATAN SITI KHADIJAH',0,1,'C');
                $pdf->Cell(0,6, 'TAHUN '.$tahun,0,1,'C');
                

                // Tentukan ukuran gambar
                $w = 60;  // lebar
                $h = 60;  // tinggi

                // Hitung posisi tengah
                $x = ($pdf->GetPageWidth() - $w) / 2;
                $y = ($pdf->GetPageHeight() - $h) / 2;

                // Tampilkan gambar
                $pdf->Image($gambar, $x, $y, $w, $h);

                $pdf->AddPage();
                $path = FCPATH . "filedata/mutufix.pdf";
                $pdf->setSourceFile($path);
                // import page 1
                $tplIdx = $pdf->importPage($i);
                $pdf->SetXY(0, 0);
                // use the imported page and place it at position 10,10 with a width of 100 mm
                $pdf->useTemplate($tplIdx, 0, 0, 297);

                $pdf->SetFont('Arial', 'B', 14);
                $pdf->SetXY(0, 49);
                $pdf->Cell(30.6,6, '',0,0,'C');
                $pdf->Cell(155.6,6, $audit['form_nama'],0,1,'C');
                $pdf->SetFont('Arial', '', 11);
                $pdf->SetXY(208, 19);
                $pdf->Write(6, $audit['form_kode']);

                $pdf->SetXY(208, 28);
                $tgl_form = date("d-m-Y", strtotime($audit['form_update']));
                $pdf->Write(6, $tgl_form);

                $pdf->SetXY(60, 61.6);
                $pdf->Write(6, $tgl_audit);

                $pdf->SetXY(60, 69);
                $pdf->Write(6, $audit['unit']);
                $pdf->SetXY(60, 76.4);
                $pdf->Write(6, $audit['auditor']);
                $pdf->SetXY(60, 83.8);
                $pdf->Write(6, $audit['auditee']);

                $pdf->SetFont('Arial', '', 10);
                $dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
                
                                
                                // Set lebar kolom
                $pdf->SetWidths([
                    11, 31.5, 49, 50, 37.45,
                    11.05, 10.05, 12.5, 12.5, 52.5
                ]);

                $pdf->SetAligns(['C','L','L','L','L','C','C','C','C','L']);

                $pdf->SetXY(8.7, 109.4); // posisi awal tabel

                $j = 1;

                foreach($dtform as $row){
                    $jwb = $this->auditjawab->getAuditJawabFix($audit['audit_id'],$row['dtform_id']);
                    if(isset($jwb) && count($jwb) > 0){
                    $detail = $this->auditjawab->barisTilik($audit['audit_id'],$row['dtform_id']);
                    if(isset($detail) && count($detail) > 0){
                    foreach($detail as $dtjwb):
                        if($dtjwb["dtjwb_temuan"] == "S"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                $dtjwb['dtjwb_temuan'],
                                "",
                                "",
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else if($dtjwb["dtjwb_temuan"] == "OB"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                $dtjwb['dtjwb_temuan'],
                                "",
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else if($dtjwb["dtjwb_temuan"] == "TS MINOR"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                "",
                                $dtjwb['dtjwb_temuan'],
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else if($dtjwb["dtjwb_temuan"] == "TS MAYOR"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                "",
                                "",
                                $dtjwb['dtjwb_temuan'],
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else{
                            $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                "",
                                "",
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }
                        $j++;      
                    endforeach;
                    }
                }
                }
               // $i = $i + 2 + $j;

            }
            


            $pdf->Output('I', 'generated.pdf');
        }else{
            $this->session->set_flashdata('pesanerror', 'Tidak ada data yang dipilih!');
            redirect(base_url('admin/daftaraudit'));
        }
    }
*/
    public function index() {
            $this->data['content'] = 'daftaraudit/index';
            $this->data['title'] = 'Daftar Audit';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['auditmenu'] = 'active';
            $this->data['audit'] = 'active';
            /* Filter periode: bawaan = periode aktif; bila tidak ada periode
               aktif, seluruh data ditampilkan (periode_id = NULL). */
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

    public function detail($id) {
        if(isset($id)){
            $this->data['content'] = 'daftaraudit/detail';
            $this->data['title'] = 'Daftar Audit';
            $this->data['audit_id'] = $id;
            $this->data['audit'] = 'active';//auditmenu
            $this->data['auditmenu'] = 'active';
            $this->data['result'] = $this->mutu->getAuditById($id);
            /* Susunan pertanyaan + butir lingkup + jawaban auditee + lampiran
               persis seperti halaman detail auditor: kuerinya ada pada model
               bersama application/models/PetaauditModel.php. */
            $this->load->model('PetaauditModel', 'petaaudit');
            $this->data['topik'] = $this->petaaudit->peta($id);
            /* Catatan "Kelebihan" & "Peluang untuk peningkatan" per butir
               lingkup (tabel mutu_lingkup_nilai). Bila tabelnya belum
               diimpor, bagiannya disembunyikan dan halaman menampilkan
               keterangan - sama seperti halaman auditor. */
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
     * Simpan catatan "Kelebihan" dan "Peluang untuk peningkatan" untuk satu
     * butir lingkup (halaman detail audit).
     *
     * Endpoint ini sama dengan milik auditor: data tersimpan pada
     * mutu_lingkup_nilai, dipisahkan per audit dan per butir lingkup,
     * sehingga tidak bergantung pada baris tilik.
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
        $this->load->model('PetaauditModel', 'petaaudit');
        if (!$this->petaaudit->lingkupMilikAudit($audit_id, $lingkup_id)) {
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

    /**
     * Jumlah audit per status untuk filter periode terpilih.
     * Dipakai kartu statistik agar ikut berubah saat filter diganti.
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

   public function tambah() {
        $form_id = $this->input->post('form_id');
        if($form_id){
                /* Tim auditor: boleh lebih dari satu; satu di antaranya
                   ketua. Bila id ketua tidak dikirim (formulir lama),
                   auditor yang dipilih pertama menjadi ketua. */
                $tim      = $this->_timTerpilih();
                $ketua_id = (int) $this->input->post('auditor_ketua');

                if (empty($tim)) {
                    $query = array("status" => false, "pesan" => "Pilih minimal satu auditor");
                    header('Access-Control-Allow-Origin: *');
                    header('Content-Type: application/json');
                    echo json_encode($query);
                    return;
                }

                if ($ketua_id < 1 || !in_array($ketua_id, $tim)) {
                    $ketua_id = (int) $tim[0];
                }

                $data = array();
                $data['form_id'] = $form_id;
                $data['auditor_id'] = $ketua_id;   // kolom lama = ketua tim
                $data['auditee_id']  = $this->input->post('auditee');

                $akunauditee = $this->akun->getAkunById($data['auditee_id']);
                $data['auditee'] = $akunauditee['nama'];
                $data['unit'] = $akunauditee['unitkerja'];

                $akunauditor = $this->akun->getAkunById($ketua_id);
                $data['auditor'] = $akunauditor['nama'];

                // Audit baru ditempatkan pada periode yang sedang aktif (bila ada).
                // Kolom periode_id hanya diisi bila sudah disiapkan, supaya
                // aplikasi tetap jalan sebelum database/periode_audit.sql dijalankan.
                if ($this->db->field_exists('periode_id', 'mutu_audit')) {
                    $data['periode_id'] = $this->periode->aktifId();
                }

                if ($this->mutu->add($data)) {
                    /* Susunan tim disimpan terpisah (mutu_auditteams). Bila
                       tabelnya belum diimpor, audit tetap tersimpan dengan
                       satu auditor pada mutu_audit.auditor_id. */
                    $this->load->model('TimauditModel', 'timaudit');
                    $this->timaudit->simpan($this->db->insert_id(), $tim, $ketua_id);

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

    /**
     * Daftar users_id auditor yang dipilih pada formulir tambah audit.
     *
     * Hanya id yang benar-benar berrole AUDITOR yang diterima, urut
     * sesuai pilihan pada formulir (ketua dipilih terpisah).
     *
     * @return array
     */
    private function _timTerpilih() {
        $boleh = array();
        foreach ($this->akun->getAllAuditor() as $a) {
            $boleh[(int) $a['users_id']] = TRUE;
        }

        $kirim = $this->input->post('auditor');
        if ($kirim === NULL) {
            /* Formulir lama mengirim satu auditor pada kolom 'auditor'. */
            $kirim = $this->input->post('auditor_id');
        }
        if (!is_array($kirim)) {
            $kirim = ($kirim === NULL || $kirim === '') ? array() : array($kirim);
        }

        $tim = array();
        foreach ($kirim as $uid) {
            $uid = (int) $uid;
            if ($uid > 0 && isset($boleh[$uid]) && !in_array($uid, $tim)) {
                $tim[] = $uid;
            }
        }

        return $tim;
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

    public function hapus() {
        $id = $this->input->post('id');
        $status = $this->mutu->hapus($id);
        /* Baris tim auditor ikut dibuang bersama auditnya. */
        if ($status) {
            $this->load->model('TimauditModel', 'timaudit');
            $this->timaudit->hapusAudit($id);
        }
        $query = array("status" => $status);
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
        // '' / 0 = semua periode
        $post['periode_id'] = $this->input->post('periode_id');

        $list = $this->mutu->get_datatables($post['length'], $post['start'], $post['search'], $post['order'], $post['periode_id']);

        /* Tim auditor (ketua + anggota) untuk audit pada halaman ini,
           diambil satu kueri supaya ringan. */
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
            $row[] = '<input type="checkbox" class="pilih" value="'.$field->audit_id.'"> '.$no;
            $row[] = $field->form_nama;
            /* Kolom Auditor: seluruh nama tim, ketua ditandai "(Ketua)".
               Audit lama yang belum punya tim tetap menampilkan
               mutu_audit.auditor. */
            $baris_tim = isset($peta_tim[(int) $field->audit_id]) ? $peta_tim[(int) $field->audit_id] : array();
            /* Satu nama per baris: dipisahkan baris baru lalu diubah
               menjadi <br> setelah karakter HTML-nya disaring. */
            $nama_tim = $this->timaudit->teks($baris_tim, $field->auditor, "\n");
            $row[] = '<div class="tim-teks">' . nl2br(html_escape($nama_tim)) . '</div>';
            $row[] = $field->auditee;
            $row[] = $field->unit;
            
            /* ---- kolom Aksi: tombol ikon seragam, info muncul saat hover ---- */
            $aksi = '<button type="button" class="detail btn btn-sm btn-icon btn-primary" aria-label="Detail audit"'
                . ' data-info="Lihat rincian audit ini" id=' . $field->audit_id . '>'
                . '<i class="icon md-book" aria-hidden="true"></i></button>';

            if ($field->audit_status == "DRAFT") {
                $aksi .= ' <button type="button" class="delete btn btn-sm btn-icon btn-danger" aria-label="Hapus audit"'
                    . ' data-info="Hapus audit ini" id=' . $field->audit_id . '>'
                    . '<i class="icon md-delete" aria-hidden="true"></i></button>';
            } else if ($field->audit_status == "SELESAI") {
                $aksi .= ' <button type="button" class="download btn btn-sm btn-icon btn-success" aria-label="Unduh Laporan AMI"'
                    . ' data-info="Unduh Laporan AMI" id=' . $field->audit_id . '>'
                    . '<i class="icon md-download" aria-hidden="true"></i></button>';
            }
            $row[] = '<div class="tabel-aksi">' . $aksi . '</div>';

            /* ---- kolom Status: badge, bukan tombol ---- */
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
            "recordsFiltered" => $this->mutu->count_filtered($post['search'], $post['order'], $post['periode_id']),
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
