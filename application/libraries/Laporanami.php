<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * LAPORAN AUDIT MUTU INTERNAL (AMI) dalam bentuk berkas Excel .xlsx.
 *
 * Bentuknya mereplikasi formulir kertas Laporan Audit Mutu Internal:
 *   - judul dokumen,
 *   - I. Pendahuluan (unit, alamat, Ka.Bag, tanggal, ketua & anggota
 *     auditor, tanda tangan),
 *   - II. Tujuan Audit (6 tujuan baku + tujuan lain, bertanda centang),
 *   - III. Lingkup Audit,
 *   - IV. Jadwal Audit (5 baris),
 *   - V. Temuan Audit (3 baris),
 *   - VI. Peluang Peningkatan (3 baris),
 *   - VII. Kesimpulan Audit (3 pertanyaan, bertanda centang),
 *   - VIII. Lampiran Audit (5 lampiran, bertanda centang),
 *   - footer kepemilikan dokumen + Paraf PPM.
 *
 * Isian yang tersedia pada aplikasi diisi otomatis dari data audit:
 *   Program Studi/Unit  <- mutu_audit.unit
 *   Nama Ka.Bag         <- mutu_audit.auditee
 *   Tanggal Audit       <- tanggal butir tilik dibuat (dtjwb_create)
 *   Ketua Auditor       <- mutu_audit.auditor (ketua tim)
 *   Anggota Auditor     <- anggota tim auditor (mutu_auditteams)
 *   Lingkup Audit       <- butir lingkup (mutu_lingkup)
 *   Temuan Audit        <- temuan OB/TS pada mutu_auditjawabdetail
 *   Peluang Peningkatan <- mutu_lingkup_nilai (kelebihan & peluang)
 *   Kesimpulan Audit    <- mutu_auditkesimpulan (diisi lewat modal)
 *
 * Kolom yang tidak tersedia pada aplikasi (Alamat, Jadwal, tanda centang
 * Tujuan & Lampiran) dibiarkan kosong untuk diisi langsung pada Excel.
 * Tanda centang dipilih lewat daftar pilihan pada sel Excel.
 * Seluruh sel dapat diedit; tidak ada informasi di luar dokumen referensi.
 */
class Laporanami {

    /** Lebar kolom A..H (proporsional untuk A4 portrait). */
    var $lebar = array(
        'A' => 6, 'B' => 13, 'C' => 13, 'D' => 19,
        'E' => 13, 'F' => 19, 'G' => 13, 'H' => 13,
    );

    /** Tujuan audit baku (Bagian II). */
    var $tujuan = array(
        'Memastikan bahwa standar dikti, manual, prosedur dan instruksi kerja telah sesuai dengan peraturan perundangan yang berlaku.',
        'Memastikan bahwa implementasi standar dikti telah sesuai dengan standar yang telah ditetapkan dan dilaksanakan sesuai dengan manual, prosedur dan instruksi kerja yang telah ditetapkan.',
        'Memetakan kesiapan Program Studi dalam melaksanakan program Akreditasi.',
        'Memastikan kelancaran pelaksanaan pengelolaan Unit.',
        'Memetakan peluang peningkatan mutu Unit.',
        'Tujuan lain, sebutkan:',
    );

    /** Lampiran audit baku (Bagian VIII). */
    var $lampiran = array(
        'PTK (Permintaan Tindakan Koreksi).',
        'Daftar Hadir.',
        'Berita Acara.',
        'Dokumentasi Kegiatan.',
        'Pemantauan PTK.',
    );

    function __construct() {
        require_once APPPATH . 'third_party/PHPExcel.php';
        require_once APPPATH . 'third_party/PHPExcel/IOFactory.php';
    }

    /* ==================================================================
       BANGUN BERKAS
       ================================================================== */

    /**
     * Bangun buku kerja Laporan AMI dari data audit.
     *
     * @param  array $data kunci: unit, alamat, kabag, tanggal_audit,
     *                      ketua_auditor, anggota (array), tujuan (array
     *                      indeks tercentang), tujuan_lain, lingkup
     *                      (array), jadwal (array), temuan (array),
     *                      peluang (array), kesimpulan (array),
     *                      lampiran (array indeks tercentang)
     * @return PHPExcel
     */
    public function buat($data) {
        $data = is_array($data) ? $data : array();

        $excel = new PHPExcel();
        $excel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('Laporan AMI');

        foreach ($this->lebar as $kolom => $lebar) {
            $sheet->getColumnDimension($kolom)->setWidth($lebar);
        }

        $this->_judul($sheet);
        $this->_pendahuluan($sheet, $data);
        $this->_tujuan($sheet, $data);
        $this->_lingkup($sheet, $data);
        $this->_jadwal($sheet, $data);
        $this->_temuan($sheet, $data);
        $this->_peluang($sheet, $data);
        $this->_kesimpulan($sheet, $data);
        $this->_lampiran($sheet, $data);
        $this->_footer($sheet);
        $this->_aturCetak($sheet);

        return $excel;
    }

    /**
     * Kirim buku kerja sebagai unduhan .xlsx.
     *
     * @param  PHPExcel $excel
     * @param  string   $nama_berkas
     */
    public function kirim($excel, $nama_berkas) {
        $nama = trim(preg_replace('/[^A-Za-z0-9_\-]+/', '-', (string) $nama_berkas), '-');
        $nama = preg_replace('/-{2,}/', '-', $nama);
        if ($nama === '') {
            $nama = 'Laporan-AMI';
        }

        /* Excel 2007 (.xlsx) memakai Zip. Bila ekstensi Zip tidak tersedia
           di server, pakai PclZip yang sudah ikut bersama PHPExcel. */
        if (!class_exists('ZipArchive')) {
            PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        $tulis = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $tulis->save('php://output');

        $excel->disconnectWorksheets();
        unset($excel);
        exit;
    }

    /* ==================================================================
       BAGIAN-BAGIAN DOKUMEN
       ================================================================== */

    /** Judul dokumen (A1:H2). */
    private function _judul($sheet) {
        $this->_isi($sheet, 'A1:H2', 'LAPORAN AUDIT MUTU INTERNAL', array(
            'tebal' => TRUE, 'ukuran' => 14, 'rata' => 'tengah',
            'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
    }

    /** I. PENDAHULUAN (baris 4-13). */
    private function _pendahuluan($sheet, $data) {
        $label = array('vertikal' => 'tengah', 'bungkus' => TRUE);
        $isian = array('vertikal' => 'atas', 'bungkus' => TRUE);

        $this->_isi($sheet, 'A4:H4', 'I. PENDAHULUAN', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));

        $this->_isi($sheet, 'A5:B5', 'Program Studi/Unit', $label);
        $this->_isi($sheet, 'C5:H5', $this->_bersih(isset($data['unit']) ? $data['unit'] : ''), $isian);

        $this->_isi($sheet, 'A6:B6', 'Alamat', $label);
        $this->_isi($sheet, 'C6:H6', $this->_bersih(isset($data['alamat']) ? $data['alamat'] : ''), $isian);

        $this->_isi($sheet, 'A7:B7', 'Nama Ka.Bag', $label);
        $this->_isi($sheet, 'C7:H7', $this->_bersih(isset($data['kabag']) ? $data['kabag'] : ''), $isian);

        $sheet->getRowDimension(8)->setRowHeight(6);

        $this->_isi($sheet, 'A9:B9', 'Tanggal Audit', $label);
        $this->_isi($sheet, 'C9:D9', $this->_bersih(isset($data['tanggal_audit']) ? $data['tanggal_audit'] : ''), $isian);

        /* Ketua Auditor dan Perguruan Tinggi: label beserta isian pada satu
           sel gabungan (E9:F9 dan G9:H9). */
        $ketua = $this->_bersih(isset($data['ketua_auditor']) ? $data['ketua_auditor'] : '');
        $this->_isi($sheet, 'E9:F9', 'Ketua Auditor: ' . $ketua, $isian);
        $this->_isi($sheet, 'G9:H9', 'Perguruan Tinggi: STIK Siti Khadijah', $isian);

        $anggota = isset($data['anggota']) && is_array($data['anggota']) ? $data['anggota'] : array();
        $teks_anggota = array();
        foreach ($anggota as $nama) {
            $nama = $this->_bersih($nama);
            if ($nama !== '') {
                $teks_anggota[] = $nama;
            }
        }
        $this->_isi($sheet, 'A10:B10', 'Anggota Auditor', $label);
        $this->_isi($sheet, 'C10:H11', implode("\n", $teks_anggota), $isian);

        $this->_isi($sheet, 'A12:B13', 'Tanda Tangan Auditor', $label);
        $this->_kotak($sheet, 'C12:D13');
        $this->_isi($sheet, 'E12:F13', 'Tanda Tangan Ka.Bag', $label);
        $this->_kotak($sheet, 'G12:H13');

        /* Tinggi baris: mengikuti isian yang membungkus. */
        $sheet->getRowDimension(4)->setRowHeight(18);
        $sheet->getRowDimension(5)->setRowHeight(max(18, $this->_tinggiTeks($data['unit'], 83)));
        $sheet->getRowDimension(6)->setRowHeight(max(18, $this->_tinggiTeks($data['alamat'], 83)));
        $sheet->getRowDimension(7)->setRowHeight(max(18, $this->_tinggiTeks($data['kabag'], 83)));
        $sheet->getRowDimension(9)->setRowHeight(max(18, $this->_tinggiTeks('Ketua Auditor: ' . $ketua, 32)));
        $tinggi_anggota = max(36, $this->_tinggiTeks(implode("\n", $teks_anggota), 83));
        $sheet->getRowDimension(10)->setRowHeight($tinggi_anggota / 2);
        $sheet->getRowDimension(11)->setRowHeight($tinggi_anggota / 2);
        $sheet->getRowDimension(12)->setRowHeight(22);
        $sheet->getRowDimension(13)->setRowHeight(22);
    }

    /** II. TUJUAN AUDIT (baris 15-24). */
    private function _tujuan($sheet, $data) {
        $centang = isset($data['tujuan']) && is_array($data['tujuan']) ? $data['tujuan'] : array();

        $this->_isi($sheet, 'A15:H15', 'II. TUJUAN AUDIT', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));
        $this->_isi($sheet, 'A16:H16', 'Beri tanda √ sesuai yang dikerjakan.', array(
            'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));

        $baris = 17;
        foreach ($this->tujuan as $i => $teks) {
            $tanda = in_array($i, $centang) ? '☒' : '☐';
            $isi = ($i + 1) . '. ' . $teks;
            $this->_isi($sheet, 'A' . $baris, $tanda, array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_centang($sheet, 'A' . $baris);
            $this->_isi($sheet, 'B' . $baris . ':H' . $baris, $isi, array(
                'vertikal' => 'atas', 'bungkus' => TRUE,
            ));
            $sheet->getRowDimension($baris)->setRowHeight(max(18, $this->_tinggiTeks($isi, 103)));
            $baris++;
        }

        /* Area pengisian tujuan lain (A23:H24). */
        $lain = $this->_bersih(isset($data['tujuan_lain']) ? $data['tujuan_lain'] : '');
        $this->_isi($sheet, 'A23:H24', $lain, array('vertikal' => 'atas', 'bungkus' => TRUE));
        $tinggi_lain = max(36, $this->_tinggiTeks($lain, 109));
        $sheet->getRowDimension(23)->setRowHeight($tinggi_lain / 2);
        $sheet->getRowDimension(24)->setRowHeight($tinggi_lain / 2);

        $sheet->getRowDimension(15)->setRowHeight(18);
        $sheet->getRowDimension(16)->setRowHeight(18);
    }

    /** III. LINGKUP AUDIT (baris 26-30). */
    private function _lingkup($sheet, $data) {
        $this->_isi($sheet, 'A26:H26', 'III. LINGKUP AUDIT', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));
        $this->_isi($sheet, 'A27', '1.', array('rata' => 'tengah', 'vertikal' => 'atas', 'bungkus' => TRUE));

        $lingkup = isset($data['lingkup']) && is_array($data['lingkup']) ? $data['lingkup'] : array();
        $teks = array();
        foreach ($lingkup as $l) {
            $l = $this->_bersih($l);
            if ($l !== '') {
                $teks[] = $l;
            }
        }
        $isi = implode("\n", $teks);
        $this->_isi($sheet, 'B27:H30', $isi, array('vertikal' => 'atas', 'bungkus' => TRUE));

        $sheet->getRowDimension(26)->setRowHeight(18);
        $tinggi = max(72, $this->_tinggiTeks($isi, 103));
        for ($b = 27; $b <= 30; $b++) {
            $sheet->getRowDimension($b)->setRowHeight($tinggi / 4);
        }
    }

    /** IV. JADWAL AUDIT (baris 32-41). */
    private function _jadwal($sheet, $data) {
        $kepala = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE);
        $isian  = array('vertikal' => 'atas', 'bungkus' => TRUE);

        $this->_isi($sheet, 'A32:H32', 'IV. JADWAL AUDIT', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));
        $this->_isi($sheet, 'A33:B33', 'Hari/Tanggal audit', array('vertikal' => 'tengah', 'bungkus' => TRUE));
        $hari = isset($data['jadwal_hari']) ? $this->_bersih($data['jadwal_hari']) : '';
        $this->_isi($sheet, 'C33:H33', $hari, $isian);

        $sheet->getRowDimension(34)->setRowHeight(6);

        $this->_isi($sheet, 'A35:A36', 'No.', $kepala);
        $this->_isi($sheet, 'B35:C36', 'Jam', $kepala);
        $this->_isi($sheet, 'D35:H36', 'Kegiatan Audit', $kepala);

        $jadwal = isset($data['jadwal']) && is_array($data['jadwal']) ? $data['jadwal'] : array();
        for ($i = 0; $i < 5; $i++) {
            $baris = 37 + $i;
            $jam = isset($jadwal[$i]['jam']) ? $this->_bersih($jadwal[$i]['jam']) : '';
            $kegiatan = isset($jadwal[$i]['kegiatan']) ? $this->_bersih($jadwal[$i]['kegiatan']) : '';

            $this->_isi($sheet, 'A' . $baris, (string) ($i + 1), array(
                'rata' => 'tengah', 'vertikal' => 'atas', 'bungkus' => TRUE, 'angka' => TRUE,
            ));
            $this->_isi($sheet, 'B' . $baris . ':C' . $baris, $jam, $isian);
            $this->_isi($sheet, 'D' . $baris . ':H' . $baris, $kegiatan, $isian);
            $sheet->getRowDimension($baris)->setRowHeight(max(18, $this->_tinggiTeks($kegiatan, 77)));
        }

        $sheet->getRowDimension(32)->setRowHeight(18);
        $sheet->getRowDimension(33)->setRowHeight(18);
        $sheet->getRowDimension(35)->setRowHeight(15);
        $sheet->getRowDimension(36)->setRowHeight(15);
    }

    /** V. TEMUAN AUDIT (baris 43-48). */
    private function _temuan($sheet, $data) {
        $kepala = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE);
        $isian  = array('vertikal' => 'atas', 'bungkus' => TRUE);

        $this->_isi($sheet, 'A43:H43', 'V. TEMUAN AUDIT', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));

        $this->_isi($sheet, 'A44:A45', 'No.', $kepala);
        $this->_isi($sheet, 'B44:C44', 'Kategori Temuan', $kepala);
        $this->_isi($sheet, 'B45:C45', 'KTS/OB', $kepala);
        $this->_isi($sheet, 'D44:E45', 'Indikator (butir mutu)', $kepala);
        $this->_isi($sheet, 'F44:H45', 'Pernyataan', $kepala);

        $temuan = isset($data['temuan']) && is_array($data['temuan']) ? $data['temuan'] : array();
        $temuan = $this->_tigaBaris($temuan, array('kategori', 'indikator', 'pernyataan'));
        for ($i = 0; $i < 3; $i++) {
            $baris = 46 + $i;
            $kategori = isset($temuan[$i]['kategori']) ? $this->_bersih($temuan[$i]['kategori']) : '';
            $indikator = isset($temuan[$i]['indikator']) ? $this->_bersih($temuan[$i]['indikator']) : '';
            $pernyataan = isset($temuan[$i]['pernyataan']) ? $this->_bersih($temuan[$i]['pernyataan']) : '';

            $this->_isi($sheet, 'A' . $baris, (string) ($i + 1), array(
                'rata' => 'tengah', 'vertikal' => 'atas', 'bungkus' => TRUE, 'angka' => TRUE,
            ));
            $this->_isi($sheet, 'B' . $baris . ':C' . $baris, $kategori, $isian);
            $this->_isi($sheet, 'D' . $baris . ':E' . $baris, $indikator, $isian);
            $this->_isi($sheet, 'F' . $baris . ':H' . $baris, $pernyataan, $isian);

            $tinggi = max(30, $this->_tinggiTeks($kategori, 26), $this->_tinggiTeks($indikator, 32), $this->_tinggiTeks($pernyataan, 45));
            $sheet->getRowDimension($baris)->setRowHeight($tinggi);
        }

        $sheet->getRowDimension(43)->setRowHeight(18);
        $sheet->getRowDimension(44)->setRowHeight(15);
        $sheet->getRowDimension(45)->setRowHeight(15);
    }

    /** VI. PELUANG PENINGKATAN (baris 50-55). */
    private function _peluang($sheet, $data) {
        $kepala = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE);
        $isian  = array('vertikal' => 'atas', 'bungkus' => TRUE);

        $this->_isi($sheet, 'A50:H50', 'VI. PELUANG PENINGKATAN', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));

        $this->_isi($sheet, 'A51:A52', 'No.', $kepala);
        $this->_isi($sheet, 'B51:C52', 'Aspek/Bidang', $kepala);
        $this->_isi($sheet, 'D51:E52', 'Kelebihan', $kepala);
        $this->_isi($sheet, 'F51:H52', 'Peluang untuk peningkatan', $kepala);

        $peluang = isset($data['peluang']) && is_array($data['peluang']) ? $data['peluang'] : array();
        $peluang = $this->_tigaBaris($peluang, array('aspek', 'kelebihan', 'peluang'));
        for ($i = 0; $i < 3; $i++) {
            $baris = 53 + $i;
            $aspek = isset($peluang[$i]['aspek']) ? $this->_bersih($peluang[$i]['aspek']) : '';
            $kelebihan = isset($peluang[$i]['kelebihan']) ? $this->_bersih($peluang[$i]['kelebihan']) : '';
            $peluang_teks = isset($peluang[$i]['peluang']) ? $this->_bersih($peluang[$i]['peluang']) : '';

            $this->_isi($sheet, 'A' . $baris, (string) ($i + 1), array(
                'rata' => 'tengah', 'vertikal' => 'atas', 'bungkus' => TRUE, 'angka' => TRUE,
            ));
            $this->_isi($sheet, 'B' . $baris . ':C' . $baris, $aspek, $isian);
            $this->_isi($sheet, 'D' . $baris . ':E' . $baris, $kelebihan, $isian);
            $this->_isi($sheet, 'F' . $baris . ':H' . $baris, $peluang_teks, $isian);

            $tinggi = max(30,
                $this->_tinggiTeks($aspek, 26),
                $this->_tinggiTeks($kelebihan, 32),
                $this->_tinggiTeks($peluang_teks, 45));
            $sheet->getRowDimension($baris)->setRowHeight($tinggi);
        }

        $sheet->getRowDimension(50)->setRowHeight(18);
        $sheet->getRowDimension(51)->setRowHeight(15);
        $sheet->getRowDimension(52)->setRowHeight(15);
    }

    /** VII. KESIMPULAN AUDIT (baris 57-67). */
    private function _kesimpulan($sheet, $data) {
        $k = isset($data['kesimpulan']) && is_array($data['kesimpulan']) ? $data['kesimpulan'] : array();
        $k1 = isset($k['kesimpulan1']) ? $k['kesimpulan1'] : '';
        $k1_lain = isset($k['kesimpulan1_lainnya']) ? $this->_bersih($k['kesimpulan1_lainnya']) : '';
        $k2 = isset($k['kesimpulan2']) ? $k['kesimpulan2'] : '';
        $k2_lain = isset($k['kesimpulan2_lainnya']) ? $this->_bersih($k['kesimpulan2_lainnya']) : '';
        $k3 = isset($k['kesimpulan3']) ? $k['kesimpulan3'] : '';

        $pertanyaan = array(
            'Sistem dokumen cukup lengkap dan terstruktur untuk mendukung pelaksanaan Sistem Penjamin Mutu Internal.',
            'Program studi/unit telah menjalankan Sistem Penjamin Mutu Internal secara konsisten dan berkelanjutan.',
        );

        $this->_isi($sheet, 'A57:H57', 'VII. KESIMPULAN AUDIT', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));

        /* Pertanyaan 1 (A58:H59) + pilihan (baris 60) + keterangan (61). */
        $this->_isi($sheet, 'A58:H59', '1. ' . $pertanyaan[0], array('vertikal' => 'atas', 'bungkus' => TRUE));
        $this->_pilihan($sheet, 60, 'Pilihan', array(
            'Ya' => $k1, 'Tidak' => $k1, 'Lainnya sebutkan' => $k1,
        ));
        $this->_isi($sheet, 'A61:H61', $k1_lain, array('vertikal' => 'atas', 'bungkus' => TRUE));

        /* Pertanyaan 2 (A62:H63) + pilihan (baris 64) + keterangan (65). */
        $this->_isi($sheet, 'A62:H63', '2. ' . $pertanyaan[1], array('vertikal' => 'atas', 'bungkus' => TRUE));
        $this->_pilihan($sheet, 64, 'Pilihan', array(
            'Ya' => $k2, 'Tidak' => $k2, 'Lainnya sebutkan' => $k2,
        ));
        $this->_isi($sheet, 'A65:H65', $k2_lain, array('vertikal' => 'atas', 'bungkus' => TRUE));

        /* Pertanyaan 3 (A66) + pilihan kategori (baris 67). */
        $this->_isi($sheet, 'A66:H66', '3. Temuan pada periode audit ini adalah:', array(
            'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));
        $this->_pilihan($sheet, 67, 'Pilihan kategori', array(
            'Mayor' => $k3, 'Minor' => $k3, 'Observasi' => $k3,
        ));

        $sheet->getRowDimension(57)->setRowHeight(18);
        $sheet->getRowDimension(58)->setRowHeight(max(18, $this->_tinggiTeks('1. ' . $pertanyaan[0], 109) / 2));
        $sheet->getRowDimension(59)->setRowHeight(max(18, $this->_tinggiTeks('1. ' . $pertanyaan[0], 109) / 2));
        $sheet->getRowDimension(60)->setRowHeight(18);
        $sheet->getRowDimension(61)->setRowHeight(max(18, $this->_tinggiTeks($k1_lain, 109)));
        $sheet->getRowDimension(62)->setRowHeight(max(18, $this->_tinggiTeks('2. ' . $pertanyaan[1], 109) / 2));
        $sheet->getRowDimension(63)->setRowHeight(max(18, $this->_tinggiTeks('2. ' . $pertanyaan[1], 109) / 2));
        $sheet->getRowDimension(64)->setRowHeight(18);
        $sheet->getRowDimension(65)->setRowHeight(max(18, $this->_tinggiTeks($k2_lain, 109)));
        $sheet->getRowDimension(66)->setRowHeight(18);
        $sheet->getRowDimension(67)->setRowHeight(18);
    }

    /**
     * Satu baris pilihan kesimpulan: label pada A:B, tiga pilihan
     * bertanda centang pada C:D, E:F, G:H.
     *
     * @param  PHPExcel_Worksheet $sheet
     * @param  int                $baris
     * @param  string             $label
     * @param  array              $pilihan teks pilihan => jawaban terpilih
     */
    private function _pilihan($sheet, $baris, $label, $pilihan) {
        $this->_isi($sheet, 'A' . $baris . ':B' . $baris, $label, array(
            'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));

        $rentang = array('C' . $baris . ':D' . $baris, 'E' . $baris . ':F' . $baris, 'G' . $baris . ':H' . $baris);
        $i = 0;
        foreach ($pilihan as $teks => $terpilih) {
            $cocok = ($terpilih === $teks || ($teks === 'Lainnya sebutkan' && $terpilih === 'Lainnya'));
            $tanda = $cocok ? '☒' : '☐';
            $this->_isi($sheet, $rentang[$i], $tanda . ' ' . $teks, array(
                'vertikal' => 'tengah', 'bungkus' => TRUE,
            ));
            $this->_centang($sheet, $this->awal($rentang[$i]), $teks);
            $i++;
        }
    }

    /** VIII. LAMPIRAN AUDIT (baris 69-74). */
    private function _lampiran($sheet, $data) {
        $centang = isset($data['lampiran']) && is_array($data['lampiran']) ? $data['lampiran'] : array();

        $this->_isi($sheet, 'A69:H69', 'VIII. LAMPIRAN AUDIT', array(
            'tebal' => TRUE, 'vertikal' => 'tengah', 'bungkus' => TRUE,
        ));

        $baris = 70;
        foreach ($this->lampiran as $i => $teks) {
            $tanda = in_array($i, $centang) ? '☒' : '☐';
            $this->_isi($sheet, 'A' . $baris, $tanda, array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_centang($sheet, 'A' . $baris);
            $this->_isi($sheet, 'B' . $baris . ':H' . $baris, ($i + 1) . '. ' . $teks, array(
                'vertikal' => 'tengah', 'bungkus' => TRUE,
            ));
            $sheet->getRowDimension($baris)->setRowHeight(18);
            $baris++;
        }

        $sheet->getRowDimension(69)->setRowHeight(18);
    }

    /** Footer kepemilikan dokumen + Paraf PPM (baris 77-79). */
    private function _footer($sheet) {
        $sheet->getRowDimension(75)->setRowHeight(6);
        $sheet->getRowDimension(76)->setRowHeight(6);

        $gaya = array('ukuran' => 8, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE);

        $this->_isiKaya($sheet, 'A77:G79', array(
            array('Dokumen Mutu ini milik STIK Siti Khadijah dan ', array('ukuran' => 8)),
            array('TIDAK DIPERBOLEHKAN', array('tebal' => TRUE, 'ukuran' => 8)),
            array(' dengan cara dan alasan apapun membuat Salinan tanpa seizin Ketua Pusat Penjamin Mutu', array('ukuran' => 8)),
        ), $gaya);

        $this->_isi($sheet, 'H77:H79', 'Paraf PPM', $gaya);

        for ($b = 77; $b <= 79; $b++) {
            $sheet->getRowDimension($b)->setRowHeight(15);
        }
    }

    /* ==================================================================
       PENGATURAN CETAK
       ================================================================== */

    /**
     * A4 portrait, kolom A-H, area cetak A1:H79, lebar pas satu halaman
     * (tinggi mengikuti panjang laporan), gridlines tersembunyi.
     */
    private function _aturCetak($sheet) {
        $atur = $sheet->getPageSetup();
        $atur->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $atur->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $atur->setFitToPage(TRUE);
        $atur->setFitToWidth(1);
        /* Tinggi halaman menyesuaikan panjang laporan: tidak dipaksa satu
           halaman. */
        $atur->setFitToHeight(0);
        $atur->setHorizontalCentered(TRUE);
        $atur->setPrintArea('A1:H79');

        $sheet->setShowGridlines(FALSE);

        $margin = $sheet->getPageMargins();
        $margin->setLeft(0.5);
        $margin->setRight(0.5);
        $margin->setTop(0.5);
        $margin->setBottom(0.5);
        $margin->setHeader(0.3);
        $margin->setFooter(0.3);
    }

    /* ==================================================================
       PEMBANTU SEL
       ================================================================== */

    /** Pilihan centang dapat diganti melalui daftar pada sel Excel. */
    private function _centang($sheet, $sel, $label = '') {
        $suffix = $label !== '' ? ' ' . $label : '';
        $validasi = $sheet->getCell($sel)->getDataValidation();
        $validasi->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
        $validasi->setFormula1('"☐' . $suffix . ',☒' . $suffix . '"');
        $validasi->setShowDropDown(TRUE);
        $validasi->setShowErrorMessage(TRUE);
        $validasi->setErrorTitle('Pilihan tidak valid');
        $validasi->setError('Pilih tanda pada daftar.');
    }

    /** Kelompokkan ke tiga baris tanpa membuang data yang melebihi formulir. */
    private function _tigaBaris($items, $fields) {
        $hasil = array(array(), array(), array());
        foreach ($items as $i => $item) {
            $slot = min(2, $i);
            foreach ($fields as $field) {
                if (!isset($hasil[$slot][$field])) $hasil[$slot][$field] = '';
                $value = isset($item[$field]) ? $this->_bersih($item[$field]) : '';
                if ($i >= 2 && count($items) > 3) {
                    $value = ($i + 1) . '. ' . $value;
                }
                if ($hasil[$slot][$field] !== '') $hasil[$slot][$field] .= "\n";
                $hasil[$slot][$field] .= $value;
            }
        }
        return $hasil;
    }

    /** Isi sel gabungan (atau tunggal) beserta kotaknya. */
    private function _isi($sheet, $rentang, $nilai, $gaya = array()) {
        $this->_kotak($sheet, $rentang);

        if ($nilai !== NULL && $nilai !== '') {
            $sel = $this->awal($rentang);
            if (!empty($gaya['angka']) && is_numeric($nilai)) {
                /* ditulis sebagai angka agar kolom No bisa diurutkan */
                $sheet->setCellValue($sel, $nilai + 0);
            } else {
                $sheet->setCellValueExplicit($sel, (string) $nilai, PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }

        $this->_gaya($sheet, $rentang, $gaya);
    }

    /** Isi sel dengan teks kaya (sebagian huruf tebal). */
    private function _isiKaya($sheet, $rentang, $bagian, $gaya = array()) {
        $this->_kotak($sheet, $rentang);

        $kaya = new PHPExcel_RichText();
        foreach ($bagian as $b) {
            $teks = isset($b[0]) ? $b[0] : '';
            $g    = isset($b[1]) ? $b[1] : array();

            $jalan = $kaya->createTextRun($teks);
            $huruf = $jalan->getFont();
            $huruf->setName('Arial');
            $huruf->setSize(isset($g['ukuran']) ? (int) $g['ukuran'] : 10);
            if (!empty($g['tebal'])) {
                $huruf->setBold(TRUE);
            }
            if (!empty($g['miring'])) {
                $huruf->setItalic(TRUE);
            }
            if (!empty($g['warna'])) {
                $huruf->getColor()->setRGB($g['warna']);
            }
        }

        $sheet->setCellValue($this->awal($rentang), $kaya);
        $this->_gaya($sheet, $rentang, $gaya);
    }

    /** Gabung sel dan beri kotak (border tipis + isi putih). */
    private function _kotak($sheet, $rentang) {
        if (strpos($rentang, ':') !== FALSE) {
            $sheet->mergeCells($rentang);
        }

        $sheet->getStyle($rentang)->applyFromArray($this->_gayaKotak());
    }

    /** Gaya kotak: border hitam tipis, latar putih. */
    private function _gayaKotak() {
        return array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFFFF'),
            ),
        );
    }

    /** Gaya huruf & perataan sel. */
    private function _gaya($sheet, $rentang, $gaya = array()) {
        $atur = $sheet->getStyle($rentang)->getAlignment();
        $atur->setWrapText(!isset($gaya['bungkus']) || !empty($gaya['bungkus']));

        $vertikal = isset($gaya['vertikal']) ? $gaya['vertikal'] : 'tengah';
        if ($vertikal === 'atas') {
            $atur->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        } else {
            $atur->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }

        if (isset($gaya['rata']) && $gaya['rata'] === 'tengah') {
            $atur->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        } else {
            $atur->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }

        $huruf = $sheet->getStyle($rentang)->getFont();
        $huruf->setName('Arial');
        if (isset($gaya['ukuran'])) {
            $huruf->setSize((int) $gaya['ukuran']);
        }
        if (!empty($gaya['tebal'])) {
            $huruf->setBold(TRUE);
        }
    }

    /**
     * Perkiraan tinggi (poin) yang dibutuhkan teks pada lebar kolom
     * tertentu dengan huruf Arial 10.
     *
     * @param  string $teks
     * @param  float  $lebar jumlah satuan lebar kolom
     * @return float
     */
    private function _tinggiTeks($teks, $lebar) {
        $teks = (string) $teks;
        if ($teks === '') {
            return 0;
        }

        /* 1 satuan lebar ~ 5,25 pt; lebar rata-rata huruf Arial 10 ~ 5 pt. */
        $per_baris = max(8, (int) floor((($lebar * 5.25) / 5) * 0.95));

        $jumlah_baris = 0;
        foreach (explode("\n", $teks) as $baris) {
            $jumlah_baris += max(1, (int) ceil(strlen($baris) / $per_baris));
        }

        return $jumlah_baris * 13.5 + 6;
    }

    /** Sel pertama dari rentang (mis. 'C5:H5' -> 'C5'). */
    private function awal($rentang) {
        $bagian = explode(':', $rentang);
        return $bagian[0];
    }

    /** Bersihkan teks: buang tag HTML, rapikan spasi. */
    private function _bersih($teks) {
        $teks = strip_tags((string) $teks);
        $teks = preg_replace('/[ \t]+/', ' ', $teks);
        return trim($teks);
    }
}
