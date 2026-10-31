<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Form PTK (Permintaan Tindakan Koreksi) dalam bentuk berkas Excel.
 *
 * Bentuknya sama dengan form kertas PTK: judul, identitas (Program Studi /
 * Unit, Kaprodi, Auditor, Tanggal Audit, PTK No, Kategori, Referensi), Uraian
 * Temuan, Rencana Tindakan Koreksi, dan Tinjauan Efektifitas, ditutup footer
 * kepemilikan dokumen serta Paraf PPM.
 *
 * Tabel form DIULANG untuk setiap temuan: satu temuan = satu lembar
 * (worksheet) tersendiri bernama "PTK 001", "PTK 002", dan seterusnya,
 * masing-masing sudah terisi datanya (kolom isian tidak dikosongkan).
 *
 * Kolom yang diisi dari data PTK:
 *   Program Studi / Unit    <- mutu_audit.unit
 *   Kaprodi / Ka. Unit      <- mutu_audit.auditee
 *   Auditor                 <- nama tim audit (ketua ditandai "(Ketua)")
 *   PTK No                  <- nomor urut form pada berkas: PTK/{tahun}/001
 *   Kategori                <- kotak temuan yang sesuai dicentang (☒):
 *                              TS MAYOR -> Mayor, TS MINOR -> Minor, OB -> Observasi
 *   Referensi (Butir Mutu)  <- pertanyaan + butir lingkup temuan
 *   Uraian Temuan           <- hasil pemeriksaan dan catatan auditor
 *   Rencana Tindakan Koreksi<- dtjwb_koreksi yang ditulis auditee (bila ada)
 *
 * Tanggal Audit, tanda tangan, dan Tinjauan Efektifitas sengaja dibiarkan
 * kosong: diisi tangan pada saat penandatanganan / audit berikutnya.
 *
 * Berkas ditulis sebagai Excel 97-2003 (.xls) mengikuti Jawabanexcel, supaya
 * tidak bergantung pada ekstensi Zip di server.
 */
class Formptk {

    /** Lebar kolom A..H (proporsional untuk kertas A4 portrait). */
    var $lebar = array(
        'A' => 14, 'B' => 13, 'C' => 12, 'D' => 12,
        'E' => 12, 'F' => 13, 'G' => 12, 'H' => 12,
    );

    /** Tinggi baris: area isian dibuat lapang supaya cukup untuk menulis. */
    var $tinggi = array(
        1 => 32, 2 => 6,
        3 => 20, 4 => 20, 5 => 20, 6 => 20, 7 => 20,
        8 => 18, 9 => 18, 10 => 18,
        11 => 22, 12 => 26, 13 => 26, 14 => 26, 15 => 26, 16 => 26,
        /* Blok tanda tangan (auditor maupun teraudit) memakai 2 baris. */
        17 => 26, 18 => 26,
        19 => 22, 20 => 26, 21 => 26, 22 => 26, 23 => 26, 24 => 26, 25 => 26,
        26 => 24, 27 => 24,
        28 => 30, 29 => 26, 30 => 26, 31 => 26, 32 => 26, 33 => 26,
        34 => 26, 35 => 26,
        36 => 6, 37 => 6,
        38 => 16, 39 => 16, 40 => 16,
    );


    /** Warna biru muda untuk teks petunjuk dalam tanda kurung. */
    var $warna_petunjuk = '2E9BD6';

    /** Baris terakhir form (footer berakhir di baris 40). */
    var $baris_akhir = 40;

    /** Tinggi baris yang dipakai pada lembar sedang dibentuk (bisa membesar). */
    var $tinggi_terpakai = array();

    /* Batas tinggi form (poin) yang masih dicetak pada SATU halaman.
       Kertas A4 portrait dengan margin 0.5" menyisakan +/- 770 pt; form
       bawaan +/- 894 pt, jadi sudah menyesuaikan skala. Bila isian
       membuat form jauh lebih tinggi, cetak dialirkan ke halaman
       berikutnya (fitToHeight 0) supaya hurufnya tidak mengecil sampai
       tidak terbaca. */
    var $tinggi_maks = 1150;

    function __construct() {
        require_once APPPATH . 'third_party/PHPExcel.php';
        require_once APPPATH . 'third_party/PHPExcel/IOFactory.php';
    }

    /* ==================================================================
       SUSUN DATA
       ================================================================== */

    /**
     * Ubah daftar temuan (keluaran PtkdataModel) menjadi daftar isi form.
     *
     * @param  array $list baris temuan (objek) dari PtkdataModel
     * @return array daftar isi form, satu elemen per temuan
     */
    public function susun($list) {
        $CI =& get_instance();
        $CI->load->model('TimauditModel', 'timaudit');

        /* Nama tim audit diambil sekaligus untuk seluruh audit yang tampil. */
        $ids = array();
        if (is_array($list)) {
            foreach ($list as $r) {
                if (isset($r->audit_id) && (int) $r->audit_id > 0) {
                    $ids[] = (int) $r->audit_id;
                }
            }
        }
        $peta_tim = $CI->timaudit->peta(array_unique($ids));

        $daftar = array();
        $urut   = 0;

        if (!is_array($list)) {
            return $daftar;
        }

        foreach ($list as $r) {
            $urut++;
            $audit_id = isset($r->audit_id) ? (int) $r->audit_id : 0;

            $baris_tim = isset($peta_tim[$audit_id]) ? $peta_tim[$audit_id] : array();
            $auditor   = $CI->timaudit->teks($baris_tim, isset($r->auditor) ? $r->auditor : '');

            /* Referensi: pertanyaan lalu butirnya (bila berbeda). */
            $referensi = array();
            $pertanyaan = $this->_bersih(isset($r->dtform_pertanyaan) ? $r->dtform_pertanyaan : '');
            $butir      = $this->_bersih(isset($r->lingkup_isi) ? $r->lingkup_isi : '');
            if ($pertanyaan !== '') {
                $referensi[] = $pertanyaan;
            }
            if ($butir !== '' && $butir !== $pertanyaan) {
                $referensi[] = $butir;
            }

            /* Uraian temuan: hasil pemeriksaan lalu catatan auditor. */
            $uraian = array();
            $hasil   = $this->_bersih(isset($r->jwb_hasil) ? $r->jwb_hasil : '');
            $catatan = $this->_bersih(isset($r->jwb_catatan) ? $r->jwb_catatan : '');
            if ($hasil !== '') {
                $uraian[] = 'Hasil: ' . $hasil;
            }
            if ($catatan !== '') {
                $uraian[] = 'Catatan: ' . $catatan;
            }

            $daftar[] = array(
                'nomor'         => sprintf('PTK/%s/%03d', date('Y'), $urut),
                'prodi_unit'    => $this->_bersih(isset($r->unit) ? $r->unit : ''),
                'kaprodi'       => $this->_bersih(isset($r->auditee) ? $r->auditee : ''),
                'auditor'       => $this->_bersih($auditor),
                'tanggal_audit' => $this->_tanggal(isset($r->dtjwb_create) ? $r->dtjwb_create : ''),
                'kategori'      => strtoupper(trim((string) (isset($r->jwb_temuan) ? $r->jwb_temuan : ''))),
                'referensi'     => implode("\n", $referensi),
                'uraian'        => implode("\n\n", $uraian),
                'koreksi'       => $this->_bersih(isset($r->jwb_koreksi) ? $r->jwb_koreksi : ''),
            );
        }

        return $daftar;
    }

    /* ==================================================================
       UNDUH BERKAS
       ================================================================== */

    /**
     * Susun berkas Form PTK lalu kirim ke peramban (langsung keluar).
     *
     * @param string $nama_berkas nama berkas tanpa ekstensi
     * @param array  $daftar      keluaran susun(): satu elemen per temuan.
     *                            Kosong -> satu form kosong tetap dikirim.
     */
    public function unduh($nama_berkas, $daftar) {
        if (empty($daftar) || !is_array($daftar)) {
            $daftar = array($this->_kosong());
        }

        $excel = new PHPExcel();

        $indeks = 0;
        foreach ($daftar as $data) {
            $this->_bentuk($excel, $indeks, 'PTK ' . sprintf('%03d', $indeks + 1), $data);
            $indeks++;
        }

        $excel->setActiveSheetIndex(0);

        /* ---------------- kirim ---------------- */
        $nama = preg_replace('/[^A-Za-z0-9_\-]/', '-', (string) $nama_berkas);
        if ($nama === '') {
            $nama = 'Form-PTK';
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $nama . '.xls"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        $tulis = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $tulis->save('php://output');

        $excel->disconnectWorksheets();
        unset($excel);
        exit;
    }

    /* ==================================================================
       BENTUK SATU LEMBAR
       ================================================================== */

    /** Isi form kosong (dipakai bila tidak ada temuan). */
    private function _kosong() {
        return array(
            'nomor'         => '',
            'prodi_unit'    => '',
            'kaprodi'       => '',
            'auditor'       => '',
            'tanggal_audit' => '',
            'kategori'      => '',
            'referensi'     => '',
            'uraian'        => '',
            'koreksi'       => '',
        );
    }

    /**
     * Bangun satu lembar Form PTK.
     *
     * @param PHPExcel $excel
     * @param int      $indeks 0 = lembar bawaan PHPExcel
     * @param string   $judul  nama worksheet
     * @param array    $data   isi kolom form
     */
    private function _bentuk($excel, $indeks, $judul, $data) {
        $sheet = ($indeks === 0) ? $excel->getSheet(0) : $excel->createSheet($indeks);
        $sheet->setTitle($judul);
        $sheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        foreach ($this->lebar as $huruf => $lebar) {
            $sheet->getColumnDimension($huruf)->setWidth($lebar);
        }

        /* Tinggi baris diterapkan SETELAH seluruh isi ditulis: isian yang
           panjang dapat menambah tinggi bloknya (lihat _sesuaikanTinggi). */
        $this->tinggi_terpakai = $this->tinggi;

        /* ---- 1. judul ---- */
        $this->_blok($sheet, 'A1:H1', 'PERMINTAAN TINDAKAN KOREKSI (PTK)', array(
            'tebal' => TRUE, 'ukuran' => 11, 'rata' => 'tengah',
        ));

        /* ---- 2. identitas ---- */
        $this->_blok($sheet, 'A3:B3', 'Program Studi / Unit');
        $this->_isiForm($sheet, 'C3:H3', $this->ambil($data, 'prodi_unit'));

        $this->_blok($sheet, 'A4:B4', 'Kaprodi / Ka. Unit');
        $this->_isiForm($sheet, 'C4:H4', $this->ambil($data, 'kaprodi'));

        $this->_blok($sheet, 'A5:B6', 'Auditor');
        $this->_isiForm($sheet, 'C5:E6', $this->ambil($data, 'auditor'));
        $this->_blok($sheet, 'F5:F6', 'Tanggal Audit');
        $this->_isiForm($sheet, 'G5:H6', $this->ambil($data, 'tanggal_audit'));

        $this->_blok($sheet, 'A7:B7', 'PTK No');
        $this->_isiForm($sheet, 'C7:D7', $this->ambil($data, 'nomor'));
        $this->_blok($sheet, 'E7', 'Kategori');
        $this->_isiForm($sheet, 'F7:H7', $this->kategori($this->ambil($data, 'kategori')));

        /* Referensi: teks petunjuk "(Butir Mutu)" biru muda miring. */
        $this->_blokKaya($sheet, 'A8:B10', array(
            array('Referensi ', array('tebal' => FALSE, 'ukuran' => 10)),
            array('(Butir Mutu)', array('miring' => TRUE, 'ukuran' => 9, 'warna' => $this->warna_petunjuk)),
        ));
        $this->_isiForm($sheet, 'C8:H10', $this->ambil($data, 'referensi'), 'atas');

        /* ---- 3. uraian temuan ---- */
        $this->_blokKaya($sheet, 'A11:H11', array(
            array('Uraian Temuan ', array('tebal' => TRUE, 'ukuran' => 10)),
            array('(diisi oleh auditor & ditandatangani)', array('miring' => TRUE, 'ukuran' => 9, 'warna' => $this->warna_petunjuk)),
        ), 'tengah');
        $this->_isiForm($sheet, 'A12:H16', $this->ambil($data, 'uraian'), 'atas');

        /* Tanda tangan auditor memakai 2 baris, sama seperti blok
           tanda tangan teraudit di bawah. */
        $this->_blok($sheet, 'A17:B18', 'Tanda Tangan Auditor');
        $this->_isiForm($sheet, 'C17:D18', '');
        $this->_blok($sheet, 'E17:E18', 'Tanggal');
        $this->_isiForm($sheet, 'F17:H18', '');

        /* ---- 4. rencana tindakan koreksi ---- */
        $this->_blokKaya($sheet, 'A19:H19', array(
            array('Rencana Tindakan Koreksi ', array('tebal' => TRUE, 'ukuran' => 10)),
            array('(diisi oleh teraudit & ditandatangani)', array('miring' => TRUE, 'ukuran' => 9, 'warna' => $this->warna_petunjuk)),
        ), 'tengah');
        $this->_isiForm($sheet, 'A20:H25', $this->ambil($data, 'koreksi'), 'atas');

        $this->_blok($sheet, 'A26:B27', 'Tanda Tangan Teraudit');
        $this->_isiForm($sheet, 'C26:D27', '');
        $this->_blok($sheet, 'E26:E27', 'Tanggal');
        $this->_isiForm($sheet, 'F26:H27', '');

        /* ---- 5. tinjauan efektivitas ---- */
        $this->_blokKaya($sheet, 'A28:H28', array(
            array('Tinjauan Efektifitas Tindakan Koreksi ', array('tebal' => TRUE, 'ukuran' => 10)),
            array('(diisi oleh auditor pada audit berikutnya & ditandatangani)', array('miring' => TRUE, 'ukuran' => 9, 'warna' => $this->warna_petunjuk)),
        ), 'tengah');
        $this->_isiForm($sheet, 'A29:H33', '', 'atas');

        /* Sama seperti blok pertama: 2 baris. */
        $this->_blok($sheet, 'A34:B35', 'Tanda Tangan Auditor');
        $this->_isiForm($sheet, 'C34:D35', '');
        $this->_blok($sheet, 'E34:E35', 'Tanggal');
        $this->_isiForm($sheet, 'F34:H35', '');

        /* ---- 6. footer (baris 36-37 = jarak, tanpa border) ---- */
        $this->_blokKaya($sheet, 'A38:F40', array(
            array('Dokumen Mutu ini milik STIK Siti Khadijah dan ', array('ukuran' => 8)),
            array('TIDAK DIPERBOLEHKAN', array('tebal' => TRUE, 'ukuran' => 8)),
            array(' dengan cara dan alasan apapun membuat Salinan tanpa seizin Ketua Pusat Penjamin Mutu', array('ukuran' => 8)),
        ), 'tengah');
        $this->_blok($sheet, 'G38:H40', 'Paraf PPM', array('ukuran' => 8, 'rata' => 'tengah', 'vertikal' => 'atas'));

        foreach ($this->tinggi_terpakai as $baris => $tinggi) {
            $sheet->getRowDimension($baris)->setRowHeight($tinggi);
        }

        $this->_aturCetak($sheet, array_sum($this->tinggi_terpakai));
    }

    /* ==================================================================
       BANTUAN SEL & TEKS
       ================================================================== */

    /**
     * Tanggal Audit diambil dari tanggal butir tilik (delik) DIBUAT, yaitu
     * kolom mutu_auditjawabdetail.dtjwb_create, ditulis sebagai dd/mm/yyyy.
     *
     * @param  string $waktu nilai timestamp dari basis data
     * @return string tanggal siap cetak, atau '' bila tidak tersedia
     */
    private function _tanggal($waktu) {
        $waktu = trim((string) $waktu);
        if ($waktu === '' || strpos($waktu, '0000-00-00') === 0) {
            return '';
        }

        $waktu = strtotime($waktu);
        if (!$waktu) {
            return '';
        }

        return date('d/m/Y', $waktu);
    }

    /** Ambil kunci dari array isian dengan aman. */
    private function ambil($data, $kunci) {
        return isset($data[$kunci]) ? $data[$kunci] : '';
    }

    /**
     * Kotak pilihan kategori: yang sesuai temuan dicentang (☒).
     *
     * @param  string $temuan OB | TS MINOR | TS MAYOR | ''
     * @return string
     */
    private function kategori($temuan) {
        $centang = ($temuan === 'TS MAYOR') ? "\xE2\x98\x92" : "\xE2\x98\x90";
        $minor   = ($temuan === 'TS MINOR') ? "\xE2\x98\x92" : "\xE2\x98\x90";
        $observasi = ($temuan === 'OB') ? "\xE2\x98\x92" : "\xE2\x98\x90";

        return $centang . ' Mayor        ' . $minor . ' Minor        ' . $observasi . ' Observasi';
    }

    /**
     * Satu blok (digabung bila perlu) ber-isi teks biasa.
     *
     * @param PHPExcel_Worksheet $sheet
     * @param string             $rentang mis. 'A3:B3'
     * @param string             $nilai
     * @param array              $gaya    tebal, ukuran, rata, vertikal, warna, miring
     */
    private function _blok($sheet, $rentang, $nilai, $gaya = array()) {
        $this->_bingkai($sheet, $rentang);

        if ($nilai !== NULL && $nilai !== '') {
            $sheet->setCellValueExplicit(
                $this->awal($rentang), (string) $nilai, PHPExcel_Cell_DataType::TYPE_STRING
            );
            /* Isian yang panjang melebihi tinggi bawaan -> tinggi blok
               ditambah. Excel tidak pernah menyesuaikan tinggi sel yang
               digabung (merge) secara otomatis. */
            $this->_sesuaikanTinggi($rentang, $nilai,
                isset($gaya['ukuran']) ? (int) $gaya['ukuran'] : 10);
        }

        $this->_gaya($sheet, $rentang, $gaya);
    }

    /** Area isian form: teks rata kiri atas (atau tengah bila diminta). */
    private function _isiForm($sheet, $rentang, $nilai, $vertikal = 'tengah') {
        $this->_blok($sheet, $rentang, $nilai, array(
            'ukuran'   => 10,
            'vertikal' => $vertikal,
        ));
    }

    /**
     * Satu blok dengan TEKS KAYA (rich text): beberapa potongan teks dengan
     * gaya berbeda dalam satu sel.
     *
     * @param PHPExcel_Worksheet $sheet
     * @param string             $rentang
     * @param array              $bagian daftar array(teks, array(gaya))
     * @param string             $rata   'kiri' | 'tengah'
     */
    private function _blokKaya($sheet, $rentang, $bagian, $rata = 'kiri') {
        $this->_bingkai($sheet, $rentang);

        $kaya = new PHPExcel_RichText();
        foreach ($bagian as $b) {
            $teks = isset($b[0]) ? $b[0] : '';
            $gaya = isset($b[1]) ? $b[1] : array();

            $jalan = $kaya->createTextRun($teks);
            $huruf = $jalan->getFont();
            $huruf->setName('Arial');
            $huruf->setSize(isset($gaya['ukuran']) ? (int) $gaya['ukuran'] : 10);
            if (!empty($gaya['tebal'])) {
                $huruf->setBold(TRUE);
            }
            if (!empty($gaya['miring'])) {
                $huruf->setItalic(TRUE);
            }
            if (!empty($gaya['warna'])) {
                $huruf->getColor()->setRGB($gaya['warna']);
            }
        }

        $sheet->setCellValue($this->awal($rentang), $kaya);

        /* Ukur juga teks kepala/footer: bila melipat, barisnya ditinggikan. */
        $gabung = '';
        $ukuran = 10;
        foreach ($bagian as $b) {
            $gabung .= isset($b[0]) ? $b[0] : '';
            if (isset($b[1]['ukuran']) && (int) $b[1]['ukuran'] > $ukuran) {
                $ukuran = (int) $b[1]['ukuran'];
            }
        }
        $this->_sesuaikanTinggi($rentang, $gabung, $ukuran);

        $this->_gaya($sheet, $rentang, array('rata' => $rata, 'vertikal' => 'tengah'));
    }

    /** Border tipis + latar putih pada seluruh sel dalam rentang. */
    private function _bingkai($sheet, $rentang) {
        list($awal, $akhir) = $this->ujung($rentang);
        if ($awal !== $akhir) {
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

    /** Terapkan rata, vertikal, dan bungkus teks pada rentang. */
    private function _gaya($sheet, $rentang, $gaya = array()) {
        $atur = $sheet->getStyle($rentang)->getAlignment();
        $atur->setWrapText(TRUE);

        $vertikal = isset($gaya['vertikal']) ? $gaya['vertikal'] : 'tengah';
        $atur->setVertical($vertikal === 'atas'
            ? PHPExcel_Style_Alignment::VERTICAL_TOP
            : PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $rata = isset($gaya['rata']) ? $gaya['rata'] : 'kiri';
        if ($rata === 'tengah') {
            $atur->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        } else {
            $atur->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }

        if (isset($gaya['ukuran']) || isset($gaya['tebal'])) {
            $huruf = $sheet->getStyle($rentang)->getFont();
            $huruf->setName('Arial');
            if (isset($gaya['ukuran'])) {
                $huruf->setSize((int) $gaya['ukuran']);
            }
            if (!empty($gaya['tebal'])) {
                $huruf->setBold(TRUE);
            }
        }
    }

    /**
     * Tambah tinggi blok bila teksnya tidak muat pada tinggi bawaan.
     *
     * Excel tidak pernah menyesuaikan tinggi sel yang digabung (merge)
     * secara otomatis, maka tinggi dihitung sendiri: jumlah baris teks
     * (pemotongan baris otomatis dihitung dari lebar kolom) dikali tinggi
     * satu baris, lalu dibagi rata ke seluruh baris yang digabung.
     *
     * @param string $rentang mis. 'A12:H16'
     * @param string $teks    isi sel
     * @param int    $ukuran  ukuran font (pt)
     */
    private function _sesuaikanTinggi($rentang, $teks, $ukuran = 10) {
        $teks = (string) $teks;
        if ($teks === '') {
            return;
        }

        if (!preg_match('/^([A-Z]+)(\d+)(?::([A-Z]+)(\d+))?$/', $rentang, $m)) {
            return;
        }

        $kolom_awal  = $m[1];
        $kolom_akhir = (isset($m[3]) && $m[3] !== '') ? $m[3] : $m[1];
        $baris_awal  = (int) $m[2];
        $baris_akhir = (isset($m[4]) && $m[4] !== '') ? (int) $m[4] : (int) $m[2];

        /* Jumlah baris: baris nyata (tanda 
) ditambah pemotongan otomatis. */
        $per_baris = $this->_karakterPerBaris(
            $this->_lebarRentang($kolom_awal, $kolom_akhir), $ukuran
        );

        $jumlah_baris = 0;
        foreach (explode("\n", $teks) as $baris) {
            $panjang = strlen($baris);
            $jumlah_baris += max(1, (int) ceil($panjang / $per_baris));
        }

        /* Tinggi satu baris = 1,35 x ukuran font (sudah longgar untuk
           Arial), ditambah jarak tepi 6 pt. Karena itu isian satu baris
           tidak mengubah tinggi bawaan, sedangkan isian panjang
           menambahnya. */
        $tinggi_butuh = $jumlah_baris * ($ukuran * 1.35) + 6;

        $jumlah_sel = $baris_akhir - $baris_awal + 1;
        $tinggi_ada = 0;
        for ($b = $baris_awal; $b <= $baris_akhir; $b++) {
            $tinggi_ada += isset($this->tinggi_terpakai[$b]) ? $this->tinggi_terpakai[$b] : 20;
        }

        if ($tinggi_butuh <= $tinggi_ada) {
            return;                     // masih muat pada tinggi bawaan
        }

        $per_row = $tinggi_butuh / $jumlah_sel;
        if ($per_row > 409) {
            $per_row = 409;             // batas tinggi baris Excel
        }
        $per_row = (int) ceil($per_row);

        for ($b = $baris_awal; $b <= $baris_akhir; $b++) {
            $lama = isset($this->tinggi_terpakai[$b]) ? $this->tinggi_terpakai[$b] : 20;
            $this->tinggi_terpakai[$b] = max($lama, $per_row);
        }
    }

    /**
     * Perkiraan jumlah karakter yang muat pada satu baris.
     *
     * 1 satuan lebar kolom ~ 5,25 pt; lebar rata-rata huruf Arial ~ 0,5 x
     * ukuran font. Dikali 0,95 agar perhitungan sedikit berhati-hati
     * (lebih tinggi daripada kepotong).
     *
     * @param  int $lebar  total lebar kolom (satuan Excel)
     * @param  int $ukuran ukuran font (pt)
     * @return int
     */
    private function _karakterPerBaris($lebar, $ukuran) {
        if ($lebar <= 0 || $ukuran <= 0) {
            return 40;
        }

        $per_baris = floor((($lebar * 5.25) / ($ukuran * 0.5)) * 0.95);

        return max(8, (int) $per_baris);
    }

    /** Total lebar (satuan Excel) kolom-kolom dalam rentang. */
    private function _lebarRentang($kolom_awal, $kolom_akhir) {
        $huruf = array_keys($this->lebar);
        $i1    = PHPExcel_Cell::columnIndexFromString($kolom_awal) - 1;
        $i2    = PHPExcel_Cell::columnIndexFromString($kolom_akhir) - 1;

        $total = 0;
        for ($i = $i1; $i <= $i2; $i++) {
            if (isset($huruf[$i])) {
                $total += $this->lebar[$huruf[$i]];
            }
        }

        return $total;
    }

    /** Sel pertama dari sebuah rentang ('C3:H3' -> 'C3'). */
    private function awal($rentang) {
        list($awal, $akhir) = $this->ujung($rentang);
        return $awal;
    }

    /** Pisahkan rentang menjadi [awal, akhir]. */
    private function ujung($rentang) {
        $bagian = explode(':', $rentang);
        if (!isset($bagian[1]) || $bagian[1] === '') {
            return array($bagian[0], $bagian[0]);
        }
        return array($bagian[0], $bagian[1]);
    }

    /* ==================================================================
       PENGATURAN CETAK & TEKS
       ================================================================== */

    /**
     * Kertas A4 portrait, tanpa gridlines, di tengah halaman.
     *
     * Form yang masih muat dicetak pada SATU halaman (fit to one page).
     * Bila isian panjang membuat tinggi form melewati batas, pencetakan
     * dialirkan ke halaman berikutnya (fitToHeight 0, lebar tetap pas)
     * supaya tulisannya tidak mengecil sampai tidak terbaca.
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int                $total_tinggi tinggi seluruh baris (poin)
     */
    private function _aturCetak($sheet, $total_tinggi = 0) {
        $atur = $sheet->getPageSetup();
        $atur->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $atur->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $atur->setFitToPage(TRUE);
        $atur->setFitToWidth(1);
        $atur->setFitToHeight(($total_tinggi > 0 && $total_tinggi > $this->tinggi_maks) ? 0 : 1);
        $atur->setHorizontalCentered(TRUE);
        $atur->setVerticalCentered(TRUE);
        $atur->setPrintArea('A1:H' . $this->baris_akhir);

        $sheet->setShowGridlines(FALSE);

        $margin = $sheet->getPageMargins();
        $margin->setLeft(0.4);
        $margin->setRight(0.4);
        $margin->setTop(0.5);
        $margin->setBottom(0.5);
        $margin->setHeader(0.3);
        $margin->setFooter(0.3);
    }

    /**
     * Bersihkan teks HTML (summernote) menjadi teks polos berbaris-baris
     * siap tempel pada sel Excel.
     *
     * @param  string $html
     * @return string
     */
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

        /* Rapatkan spasi per baris, lalu buang baris kosong beruntun. */
        $baris = explode("\n", $teks);
        foreach ($baris as $k => $b) {
            $baris[$k] = trim(preg_replace('/[ \t]+/', ' ', $b));
        }
        $teks = implode("\n", $baris);
        $teks = preg_replace("/\n{3,}/", "\n\n", $teks);

        return trim($teks);
    }
}
