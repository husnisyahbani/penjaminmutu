<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * DAFTAR TILIK AMI (Audit Mutu Internal) dalam bentuk berkas Excel .xlsx.
 *
 * Bentuknya mereplikasi formulir kertas Daftar Tilik AMI:
 *   - header identitas dokumen (logo, nama institusi, kode, revisi),
 *   - informasi pelaksanaan audit (tanggal, unit kerja, auditor, auditee),
 *   - tabel bertingkat: No | Tujuan | Referensi/butir pertanyaan | Lingkup
 *     Pertanyaan | Hasil observasi | Temuan (S, OB, TS Mayor/Minor) | Catatan,
 *   - footer kepemilikan dokumen + Paraf PPM.
 *
 * Isi sel diambil dari daftar tilik yang sedang dibuka pada halaman
 * admin/delik dan auditor/delik (tabel mutu_auditjawabdetail):
 *   Tujuan                    <- mutu_auditjawab.jwb_tujuan
 *   Referensi/butir pertanyaan<- dtjwb_referensi
 *   Lingkup Pertanyaan        <- dtjwb_pertanyaan
 *   Hasil observasi           <- dtjwb_hasil
 *   S / OB / Mayor / Minor    <- tanda X pada kolom sesuai dtjwb_temuan
 *   Catatan                   <- dtjwb_catatan
 *   Tanggal Audit             <- tanggal butir tilik dibuat (dtjwb_create)
 *
 * Jumlah baris mengikuti banyaknya butir, paling sedikit 10 baris supaya
 * tersisa ruang untuk menulis manual. Tinggi baris menyesuaikan isian.
 */
class Tilikexcel {

    /** Lebar kolom A..K (proporsional untuk A4 landscape). */
    var $lebar = array(
        'A' => 8, 'B' => 22, 'C' => 26, 'D' => 32, 'E' => 32,
        'F' => 5, 'G' => 5, 'H' => 7, 'I' => 7, 'J' => 12, 'K' => 12,
    );

    /** Baris pertama isian tabel (kepala tabel menempati baris 10-12). */
    var $baris_awal = 13;

    /** Jumlah baris isian paling sedikit (ruang untuk menulis manual). */
    var $baris_minimal = 10;

    /** Tinggi baris isian bawaan (dapat membesar mengikuti isi). */
    var $tinggi_isi = 32;

    /* Batas tinggi lembar (poin) yang masih dicetak pada satu halaman.
       A4 landscape dengan margin 0.4" menyisakan +/- 500 pt. Bila isian
       membuat lembar jauh lebih tinggi, cetak dialirkan ke halaman
       berikutnya supaya hurufnya tidak mengecil sampai tidak terbaca. */
    var $tinggi_maks = 900;

    function __construct() {
        require_once APPPATH . 'third_party/PHPExcel.php';
        require_once APPPATH . 'third_party/PHPExcel/IOFactory.php';
    }

    /* ==================================================================
       SUSUN DATA
       ================================================================== */

    /**
     * Ubah baris daftar tilik (DtjwbModel::butirAudit) menjadi isi tabel.
     *
     * @param  array  $butir  daftar butir tilik
     * @param  string $tujuan jwb_tujuan pertanyaan ini
     * @return array
     */
    public function susun($butir, $tujuan = '') {
        $tujuan = $this->_bersih($tujuan);
        $daftar = array();

        if (!is_array($butir)) {
            return $daftar;
        }

        foreach ($butir as $b) {
            $daftar[] = array(
                'tujuan'     => $tujuan,
                'referensi'  => $this->_bersih(isset($b['dtjwb_referensi']) ? $b['dtjwb_referensi'] : ''),
                'pertanyaan' => $this->_bersih(isset($b['dtjwb_pertanyaan']) ? $b['dtjwb_pertanyaan'] : ''),
                'hasil'      => $this->_bersih(isset($b['dtjwb_hasil']) ? $b['dtjwb_hasil'] : ''),
                'temuan'     => strtoupper(trim((string) (isset($b['dtjwb_temuan']) ? $b['dtjwb_temuan'] : ''))),
                'catatan'    => $this->_bersih(isset($b['dtjwb_catatan']) ? $b['dtjwb_catatan'] : ''),
                'tanggal'    => isset($b['dtjwb_create']) ? $b['dtjwb_create'] : '',
            );
        }

        return $daftar;
    }

    /**
     * Tanggal Audit = tanggal butir tilik DIBUAT yang paling awal
     * (mutu_auditjawabdetail.dtjwb_create), ditulis dd/mm/yyyy.
     *
     * @param  array $baris keluaran susun()
     * @return string
     */
    public function tanggalAudit($baris) {
        $paling_awal = 0;

        foreach ((array) $baris as $b) {
            if (empty($b['tanggal'])) {
                continue;
            }
            $waktu = strtotime($b['tanggal']);
            if ($waktu && ($paling_awal === 0 || $waktu < $paling_awal)) {
                $paling_awal = $waktu;
            }
        }

        return $paling_awal ? date('d/m/Y', $paling_awal) : '';
    }

    /* ==================================================================
       UNDUH BERKAS
       ================================================================== */

    /**
     * Susun berkas Daftar Tilik AMI lalu kirim ke peramban (langsung keluar).
     *
     * @param string $nama_berkas nama berkas tanpa ekstensi
     * @param array  $data        kunci: tanggal_audit, unit, auditor, auditee,
     *                            baris (keluaran susun())
     */
    public function unduh($nama_berkas, $data) {
        $excel = new PHPExcel();
        $sheet = $excel->getSheet(0);
        $sheet->setTitle('Daftar Tilik AMI');
        $sheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        foreach ($this->lebar as $huruf => $lebar) {
            $sheet->getColumnDimension($huruf)->setWidth($lebar);
        }

        $baris   = isset($data['baris']) ? $data['baris'] : array();
        $jumlah  = max($this->baris_minimal, count($baris));
        $akhir   = $this->baris_awal + $jumlah - 1;   // baris isian terakhir
        $jarak   = $akhir + 1;                        // baris jarak sebelum footer
        $footer  = $jarak + 1;                        // baris footer

        /* tinggi baris bawaan, nanti disesuaikan pada baris isian */
        $tinggi = array();
        for ($b = 1; $b <= 4; $b++) {
            $tinggi[$b] = 22;                 // header identitas
        }
        $tinggi[5] = 8;                       // jarak
        for ($b = 6; $b <= 9; $b++) {
            $tinggi[$b] = 20;                 // informasi pelaksanaan
        }
        for ($b = 10; $b <= 12; $b++) {
            $tinggi[$b] = 20;                 // kepala tabel bertingkat
        }
        for ($b = $this->baris_awal; $b <= $akhir; $b++) {
            $tinggi[$b] = $this->tinggi_isi;  // isian
        }
        $tinggi[$jarak]  = 8;
        $tinggi[$footer] = 34;

        $this->_header($sheet);
        $this->_logo($sheet);
        $this->_pelaksanaan($sheet, $data);
        $this->_kepala($sheet);
        $this->_isian($sheet, $baris, $jumlah, $tinggi);
        $this->_footer($sheet, $footer);

        $total_tinggi = 0;
        foreach ($tinggi as $b => $h) {
            $sheet->getRowDimension($b)->setRowHeight($h);
            $total_tinggi += $h;
        }

        $this->_halaman($sheet, $total_tinggi);
        $this->_aturCetak($sheet, 'A1:K' . $footer, $total_tinggi);

        /* ---------------- kirim ---------------- */
        $nama = preg_replace('/[^A-Za-z0-9_\-]/', '-', (string) $nama_berkas);
        if ($nama === '') {
            $nama = 'Daftar-Tilik-AMI';
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
       BAGIAN-BAGIAN FORMULIR
       ================================================================== */

    /** Header identitas institusi & dokumen (baris 1-4, kolom A..K). */
    private function _header($sheet) {
        $tengah = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE);

        /* A1:A4 = logo STIK Siti Khadijah (disisipkan oleh _logo()). */
        $this->_kotak($sheet, 'A1:A4');

        $this->_isi($sheet, 'B1:E2', 'SEKOLAH TINGGI ILMU KESEHATAN SITI KHADIJAH', $tengah);
        $this->_isi($sheet, 'B3:E3', 'FORMULIR', $tengah);
        $this->_isi($sheet, 'B4:E4', 'DOKUMEN DAFTAR TILIK AMI', $tengah);

        $this->_isi($sheet, 'F1:K1', 'KODE', $tengah);
        $this->_isi($sheet, 'F2:K2', 'STIK.SK-FORM.A.04.004', $tengah);
        /* F3:H3 = nomor halaman; diisi oleh _halaman() setelah tinggi
           seluruh baris diketahui. */
        $this->_kotak($sheet, 'F3:H3');
        $this->_isi($sheet, 'I3:K3', 'Tanggal: 20 Desember 2024', $tengah);
        $this->_isi($sheet, 'F4:H4', 'Revisi', $tengah);
        $this->_isi($sheet, 'I4:K4', '02', $tengah);
    }

    /**
     * Nomor halaman pada blok header (F3:H3) dan pada footer halaman.
     *
     * Baris 1-4 diulang di setiap halaman cetak (setRowsToRepeatAtTop),
     * jadi sel F3 selalu berisi teks yang sama: "Halaman 1 dari N" dengan N
     * = jumlah halaman lembar ini. Nomor halaman yang benar per lembar
     * ditulis pada footer memakai kode Excel &P (halaman ini) dan &N
     * (total halaman), sehingga halaman 2 dan seterusnya ikut bernomor.
     */
    private function _halaman($sheet, $total_tinggi) {
        $this->_isi($sheet, 'F3:H3', 'Halaman 1 dari ' . $this->_jumlahHalaman($total_tinggi),
            array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE));

        $kepala = $sheet->getHeaderFooter();
        $kepala->setDifferentOddEven(FALSE);
        $kepala->setScaleWithDocument(TRUE);
        $kepala->setOddFooter('&RHalaman &P dari &N');
    }

    /**
     * Perkirakan jumlah halaman cetak lembar ini.
     *
     * Selama tinggi lembar masih di bawah batas, seluruhnya dikecilkan ke
     * satu halaman (fit to one page). Selebihnya mengalir ke halaman
     * berikutnya setinggi kertas A4 landscape dikurangi margin.
     *
     * @param  float $total_tinggi tinggi seluruh baris (poin)
     * @return int
     */
    private function _jumlahHalaman($total_tinggi) {
        if ($total_tinggi <= $this->tinggi_maks) {
            return 1;
        }

        /* A4 landscape = 21 cm (~595,3 pt) dikurangi margin atas/bawah
           (0,4") dan ruang header/footer cetak (0,3"). */
        $bisa_cetak = max(60, 595.3 - ((0.4 + 0.4 + 0.3 + 0.3) * 72));

        return max(1, (int) ceil($total_tinggi / $bisa_cetak));
    }

    /**
     * Sisipkan logo STIK pada blok A1:A4 bila berkasnya tersedia.
     *
     * Bila berkas tidak ada atau tidak terbaca, unduhan tetap berjalan dan
     * blok A1:A4 tersisa sebagai kotak kosong (tempat logo ditempel).
     */
    private function _logo($sheet) {
        $berkas = NULL;
        $kandidat = array(
            FCPATH . 'filedata/logostikfinal.png',
            FCPATH . 'assets/assets/images/logostik.png',
        );
        foreach ($kandidat as $c) {
            if (file_exists($c)) {
                $berkas = $c;
                break;
            }
        }

        if ($berkas === NULL || !class_exists('PHPExcel_Worksheet_Drawing')) {
            return;
        }

        try {
            $gambar = new PHPExcel_Worksheet_Drawing();
            $gambar->setName('Logo STIK');
            $gambar->setDescription('Logo STIK Siti Khadijah');
            $gambar->setPath($berkas);
            $gambar->setCoordinates('A1');

            /* Ukuran menyesuaikan blok A1:A4 (lebar kolom A x 4 baris). */
            $kotak_lebar  = $this->lebar['A'] * 5.25;
            $kotak_tinggi = 4 * 22;
            $lebar  = (float) $gambar->getWidth();
            $tinggi = (float) $gambar->getHeight();
            if ($lebar > 0 && $tinggi > 0) {
                $skala  = min($kotak_lebar / $lebar, $kotak_tinggi / $tinggi);
                $lebar  = $lebar * $skala;
                $tinggi = $tinggi * $skala;
                $gambar->setWidth($lebar);
                $gambar->setHeight($tinggi);
            }

            /* Dipusatkan di dalam kotak (offset dalam piksel). */
            $gambar->setOffsetX(max(0, PHPExcel_Shared_Drawing::pointsToPixels(($kotak_lebar - $lebar) / 2)));
            $gambar->setOffsetY(max(0, PHPExcel_Shared_Drawing::pointsToPixels(($kotak_tinggi - $tinggi) / 2)));
            $gambar->setWorksheet($sheet);
        } catch (Exception $e) {
            /* Lewatkan saja: blok logo tetap berupa kotak kosong. */
        }
    }

    /** Informasi pelaksanaan audit (baris 6-9). */
    private function _pelaksanaan($sheet, $data) {
        $kiri  = array('vertikal' => 'tengah', 'bungkus' => TRUE);
        $label = array('vertikal' => 'tengah', 'bungkus' => TRUE, 'tebal' => TRUE);

        $pasangan = array(
            6 => array('Tanggal Audit', $this->ambil($data, 'tanggal_audit')),
            7 => array('Unit Kerja',    $this->ambil($data, 'unit')),
            8 => array('Auditor',       $this->ambil($data, 'auditor')),
            9 => array('Auditee*',      $this->ambil($data, 'auditee')),
        );

        foreach ($pasangan as $b => $isi) {
            $this->_isi($sheet, 'A' . $b . ':B' . $b, $isi[0], $label);
            /* Kolom titik dua menyatu dengan kolom isian: isian diawali ':'
               supaya tanda bacanya tetap tampak seperti formulir kertas. */
            $this->_isi($sheet, 'C' . $b . ':K' . $b,
                ($isi[1] === '' ? ':' : ': ' . $isi[1]), $kiri);
        }
    }

    /** Kepala tabel bertingkat (baris 10-12). */
    private function _kepala($sheet) {
        $kepala = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE);

        $this->_isi($sheet, 'A10:A12', 'No', $kepala);
        $this->_isi($sheet, 'B10:B12', 'Tujuan', $kepala);
        $this->_isi($sheet, 'C10:C12', 'Referensi/butir pertanyaan', $kepala);
        $this->_isi($sheet, 'D10:D12', 'Lingkup Pertanyaan', $kepala);
        $this->_isi($sheet, 'E10:E12', 'Hasil observasi (catatan audit)', $kepala);

        /* Kelompok Temuan menaungi S, OB, dan TS (Mayor/Minor). */
        $this->_isi($sheet, 'F10:I10', 'Temuan', $kepala);
        $this->_isi($sheet, 'F11:F12', 'S', $kepala);
        $this->_isi($sheet, 'G11:G12', 'OB', $kepala);
        $this->_isi($sheet, 'H11:I11', 'TS', $kepala);
        $this->_isi($sheet, 'H12', 'Mayor', $kepala);
        $this->_isi($sheet, 'I12', 'Minor', $kepala);

        $this->_isi($sheet, 'J10:K12', 'Catatan', $kepala);
    }

    /**
     * Baris isian (dimulai baris 13).
     *
     * @param PHPExcel_Worksheet $sheet
     * @param array              $baris  keluaran susun()
     * @param int                $jumlah jumlah baris yang disediakan
     * @param array              $tinggi tinggi baris (diubah bila perlu)
     */
    private function _isian($sheet, $baris, $jumlah, &$tinggi) {
        $isi    = array('vertikal' => 'atas', 'bungkus' => TRUE);
        $nomor  = array('rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE, 'angka' => TRUE);
        $tanda  = array('rata' => 'tengah', 'vertikal' => 'tengah');
        $baris  = is_array($baris) ? $baris : array();

        for ($i = 0; $i < $jumlah; $i++) {
            $b = $this->baris_awal + $i;
            $r = isset($baris[$i]) ? $baris[$i] : array();
            $temuan = isset($r['temuan']) ? $r['temuan'] : '';

            $this->_isi($sheet, 'A' . $b, $i + 1, $nomor);
            $this->_isi($sheet, 'B' . $b, $this->ambil($r, 'tujuan'), $isi);
            $this->_isi($sheet, 'C' . $b, $this->ambil($r, 'referensi'), $isi);
            $this->_isi($sheet, 'D' . $b, $this->ambil($r, 'pertanyaan'), $isi);
            $this->_isi($sheet, 'E' . $b, $this->ambil($r, 'hasil'), $isi);
            $this->_isi($sheet, 'F' . $b, $this->penanda($temuan, 'S'), $tanda);
            $this->_isi($sheet, 'G' . $b, $this->penanda($temuan, 'OB'), $tanda);
            $this->_isi($sheet, 'H' . $b, $this->penanda($temuan, 'TS MAYOR'), $tanda);
            $this->_isi($sheet, 'I' . $b, $this->penanda($temuan, 'TS MINOR'), $tanda);
            $this->_isi($sheet, 'J' . $b . ':K' . $b, $this->ambil($r, 'catatan'), $isi);

            /* Tinggi baris mengikuti isian terpanjang pada kolom B..E. */
            $butuh = $this->tinggi_isi;
            foreach (array('B' => 'tujuan', 'C' => 'referensi', 'D' => 'pertanyaan', 'E' => 'hasil') as $huruf => $kunci) {
                $butuh = max($butuh, $this->_tinggiTeks(
                    $this->ambil($r, $kunci), $this->lebar[$huruf]
                ));
            }
            $butuh = min(409, (int) ceil($butuh));    // batas tinggi baris Excel
            $tinggi[$b] = max($tinggi[$b], $butuh);
        }
    }

    /** Footer kepemilikan dokumen + Paraf PPM. */
    private function _footer($sheet, $baris) {
        $gaya = array('ukuran' => 8, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE);

        $this->_isiKaya($sheet, 'A' . $baris . ':J' . $baris, array(
            array('Dokumen Mutu ini milik STIK Siti Khadijah dan ', array('ukuran' => 8)),
            array('TIDAK DIPERBOLEHKAN', array('tebal' => TRUE, 'ukuran' => 8)),
            array(' dengan cara dan alasan apapun membuat Salinan tanpa seizin Ketua Pusat Penjamin Mutu', array('ukuran' => 8)),
        ), $gaya);

        $this->_isi($sheet, 'K' . $baris, 'Paraf PPM', array(
            'ukuran' => 8, 'rata' => 'tengah', 'vertikal' => 'atas', 'bungkus' => TRUE,
        ));
    }

    /* ==================================================================
       BANTUAN
       ================================================================== */

    /** Ambil kunci dari array dengan aman. */
    public function ambil($data, $kunci) {
        return isset($data[$kunci]) ? $data[$kunci] : '';
    }

    /** Tanda X pada kolom temuan yang sesuai; selainnya dikosongkan. */
    private function penanda($temuan, $kode) {
        return ($temuan === $kode) ? 'X' : '';
    }

    /** Border tipis + latar putih pada rentang (digabung bila perlu). */
    private function _kotak($sheet, $rentang) {
        if (strpos($rentang, ':') !== FALSE) {
            $sheet->mergeCells($rentang);
        }

        $sheet->getStyle($rentang)->applyFromArray(array(
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
        ));
    }

    /**
     * Tulis teks biasa pada rentang.
     *
     * @param PHPExcel_Worksheet $sheet
     * @param string             $rentang
     * @param string             $nilai
     * @param array              $gaya tebal, ukuran, rata, vertikal, bungkus
     */
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

    /** Tulis teks kaya (beberapa gaya dalam satu sel). */
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

    /** Terapkan font & perataan pada rentang. */
    private function _gaya($sheet, $rentang, $gaya = array()) {
        $atur = $sheet->getStyle($rentang)->getAlignment();
        $atur->setWrapText(!isset($gaya['bungkus']) || !empty($gaya['bungkus']));

        $vertikal = isset($gaya['vertikal']) ? $gaya['vertikal'] : 'tengah';
        $atur->setVertical($vertikal === 'atas'
            ? PHPExcel_Style_Alignment::VERTICAL_TOP
            : PHPExcel_Style_Alignment::VERTICAL_CENTER);

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

    /** Sel pertama dari rentang ('B1:E2' -> 'B1'). */
    private function awal($rentang) {
        $bagian = explode(':', $rentang);
        return $bagian[0];
    }

    /**
     * Perkirakan tinggi (poin) yang dibutuhkan teks pada satu kolom.
     * Excel tidak pernah menyesuaikan tinggi sel yang digabung/berbungkus
     * secara otomatis, maka dihitung sendiri.
     *
     * @param  string $teks
     * @param  int    $lebar lebar kolom (satuan Excel)
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

    /** Kertas A4 landscape, tanpa gridlines, di tengah halaman. */
    private function _aturCetak($sheet, $area, $total_tinggi = 0) {
        $atur = $sheet->getPageSetup();
        $atur->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $atur->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $atur->setFitToPage(TRUE);
        $atur->setFitToWidth(1);
        $atur->setFitToHeight(($total_tinggi > 0 && $total_tinggi > $this->tinggi_maks) ? 0 : 1);
        $atur->setHorizontalCentered(TRUE);
        $atur->setVerticalCentered(TRUE);
        $atur->setPrintArea($area);

        /* Baris 1-4 adalah header identitas dokumen: diulang di setiap
           halaman cetak supaya setiap lembar tetap berkepala. */
        $atur->setRowsToRepeatAtTopByStartAndEnd(1, 4);

        $sheet->setShowGridlines(FALSE);

        $margin = $sheet->getPageMargins();
        $margin->setLeft(0.4);
        $margin->setRight(0.4);
        $margin->setTop(0.4);
        $margin->setBottom(0.4);
        $margin->setHeader(0.3);
        $margin->setFooter(0.3);
    }

    /** Bersihkan teks HTML (summernote) menjadi teks polos berbaris-baris. */
    private function _bersih($html) {
        $teks = (string) $html;
        if ($teks === '') {
            return '';
        }

        $teks = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $teks);
        $teks = preg_replace('/<br\s*\/?>/i', "\n", $teks);
        $teks = preg_replace('/<\/(p|div|li|h[1-6]|tr)>/i', "\n", $teks);
        $teks = preg_replace('/<li\b[^>]*>/i', "\n- ", $teks);
        $teks = str_replace(array('&nbsp;', '&#160;'), ' ', $teks);
        $teks = strip_tags($teks);
        $teks = html_entity_decode($teks, ENT_QUOTES, 'UTF-8');
        $teks = str_replace("\xC2\xA0", ' ', $teks);

        $baris = explode("\n", $teks);
        foreach ($baris as $k => $b) {
            $baris[$k] = trim(preg_replace('/[ \t]+/', ' ', $b));
        }
        $teks = implode("\n", $baris);
        $teks = preg_replace("/\n{3,}/", "\n\n", $teks);

        return trim($teks);
    }
}
