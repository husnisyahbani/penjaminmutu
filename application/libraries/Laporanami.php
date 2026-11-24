<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * LAPORAN AUDIT MUTU INTERNAL (AMI) dalam bentuk berkas Excel .xlsx.
 *
 * Format mengikuti dokumen resmi Laporan AMI STIK Siti Khadijah (Halaman 3 - 6):
 *   - Judul Dokumen
 *   - I.   PENDAHULUAN (tabel identitas pelaksanaan audit & tanda tangan)
 *   - II.  TUJUAN AUDIT (tabel butir tujuan & kolom centang)
 *   - III. LINGKUP AUDIT (daftar standar yang diaudit)
 *   - IV.  JADWAL AUDIT (tabel susunan acara & jam pelaksanaan)
 *   - V.   TEMUAN AUDIT (tabel temuan KTS/OB, dinamis mengikuti seluruh temuan)
 *   - VI.  PELUANG PENINGKATAN (tabel kelebihan & peluang peningkatan)
 *   - VII. KESIMPULAN AUDIT (pilihan kotak centang & rekapitulasi jumlah temuan)
 *   - VIII. LAMPIRAN AUDIT (daftar dokumen lampiran)
 */
class Laporanami {

    /** Lebar kolom A..H (proporsional untuk A4 portrait, total ~87 pt). */
    var $lebar = array(
        'A' => 5,   // No / kotak centang
        'B' => 15,  // Label / Jam / Kategori
        'C' => 12,  // Isi / Aspek
        'D' => 16,  // Indikator / Kelebihan
        'E' => 12,  // Telp / Tanda Tangan
        'F' => 14,  // Pernyataan / Peluang
        'G' => 8,   // Pernyataan / Peluang
        'H' => 5,   // Kolom centang Tujuan
    );

    /** Tujuan audit baku (Bagian II). */
    var $tujuan_baku = array(
        'Memastikan bahwa standar dikti, manual, prosedur dan instruksi kerja telah sesuai dengan peraturan perundangan yang berlaku.',
        'Memastikan bahwa implementasi standar dikti telah sesuai dengan standar yang telah ditetapkan dan dilaksanakan sesuai dengan manual, prosedur dan instruksi kerja yang telah ditetapkan.',
        'Memetakan kesiapan Program Studi dalam melaksanakan program Akreditasi.',
        'Memastikan kelancaran pelaksanaan pengelolaan Program Studi.',
        'Memetakan peluang peningkatan mutu Program Studi',
    );

    /** Jadwal audit baku (Bagian IV). */
    var $jadwal_baku = array(
        array('jam' => '09.00 – 09.15', 'kegiatan' => 'Pembukaan'),
        array('jam' => '09.15 – 11.30', 'kegiatan' => 'Pelaksanaan AMI'),
        array('jam' => '11.30 – 12.00', 'kegiatan' => 'Perumusan hasil temuan'),
        array('jam' => '13.00 – 13.30', 'kegiatan' => 'Penyampaian hasil temuan (auditor dan auditee)'),
        array('jam' => '13.30 – 14.00', 'kegiatan' => 'Laporan hasil temuan'),
    );

    /** Lampiran audit baku (Bagian VIII). */
    var $lampiran_baku = array(
        'PTK (Permintaan Tindakan Koreksi)',
        'Daftar Hadir',
        'Berita Acara',
        'Dokumentasi Kegiatan',
        'Pemantauan PTK',
    );

    function __construct() {
        require_once APPPATH . 'third_party/PHPExcel.php';
        require_once APPPATH . 'third_party/PHPExcel/IOFactory.php';
    }

    /**
     * Bangun buku kerja Laporan AMI dari data audit.
     *
     * @param  array $data
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

        $r = 1;

        // 1. Judul Dokumen (baris 1)
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'LAPORAN AUDIT MUTU INTERNAL', array(
            'tebal' => TRUE, 'ukuran' => 12, 'rata' => 'tengah', 'vertikal' => 'tengah',
        ));
        $sheet->getRowDimension($r)->setRowHeight(24);
        $r += 2;

        // 2. I. PENDAHULUAN
        $r = $this->_renderPendahuluan($sheet, $data, $r);
        $r++;

        // 3. II. TUJUAN AUDIT
        $r = $this->_renderTujuan($sheet, $data, $r);
        $r++;

        // 4. III. LINGKUP AUDIT
        $r = $this->_renderLingkup($sheet, $data, $r);
        $r++;

        // 5. IV. JADWAL AUDIT
        $r = $this->_renderJadwal($sheet, $data, $r);
        $r++;

        // 6. V. TEMUAN AUDIT
        $r = $this->_renderTemuan($sheet, $data, $r);
        $r++;

        // 7. VI. PELUANG PENINGKATAN
        $r = $this->_renderPeluang($sheet, $data, $r);
        $r++;

        // 8. VII. KESIMPULAN AUDIT
        $r = $this->_renderKesimpulan($sheet, $data, $r);
        $r++;

        // 9. VIII. LAMPIRAN AUDIT
        $r = $this->_renderLampiran($sheet, $data, $r);

        // Pengaturan Cetak A4 Portrait
        $this->_aturCetak($sheet, $r);

        return $excel;
    }

    /**
     * I. PENDAHULUAN
     */
    private function _renderPendahuluan($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'I.   PENDAHULUAN', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        $unit   = isset($data['unit']) ? $this->_bersih($data['unit']) : '';
        $alamat = !empty($data['alamat']) ? $this->_bersih($data['alamat']) : 'Jl. Demang Lebar Daun Pakjo Palembang';
        $kabag  = isset($data['kabag']) ? $this->_bersih($data['kabag']) : '';
        $tgl    = isset($data['tanggal_audit']) ? $this->_tglIndo($data['tanggal_audit']) : '';
        $ketua  = isset($data['ketua_auditor']) ? $this->_bersih($data['ketua_auditor']) : '';
        $pt     = !empty($data['pt']) ? $this->_bersih($data['pt']) : 'STIK Siti Khadijah';

        $anggota = isset($data['anggota']) && is_array($data['anggota']) ? $data['anggota'] : array();
        $anggota_teks = array();
        foreach ($anggota as $idx => $a) {
            $a = $this->_bersih($a);
            if ($a !== '') {
                $anggota_teks[] = (count($anggota) > 1 ? ($idx + 1) . '. ' : '') . $a;
            }
        }
        $anggota_str = implode("\n", $anggota_teks);

        $label_kaprodi = (stripos($unit, 'prodi') !== FALSE || stripos($unit, 'keperawatan') !== FALSE || stripos($unit, 'kebidanan') !== FALSE || stripos($unit, 'farmasi') !== FALSE)
            ? 'Nama Kaprodi' : 'Nama Ka.Unit';
        $label_ttd_kaprodi = (stripos($unit, 'prodi') !== FALSE || stripos($unit, 'keperawatan') !== FALSE || stripos($unit, 'kebidanan') !== FALSE || stripos($unit, 'farmasi') !== FALSE)
            ? 'TandaTangan Kaprodi :' : 'TandaTangan Ka.Unit :';

        // Baris 1: Program Studi/Unit
        $this->_kotakIsi($sheet, 'A' . $r . ':B' . $r, 'Program Studi/Unit', array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'C' . $r . ':H' . $r, $unit, array('vertikal' => 'tengah'));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        // Baris 2: Alamat
        $this->_kotakIsi($sheet, 'A' . $r . ':B' . $r, 'Alamat', array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'C' . $r . ':H' . $r, $alamat, array('vertikal' => 'tengah'));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        // Baris 3: Nama Kaprodi & Telp
        $this->_kotakIsi($sheet, 'A' . $r . ':B' . $r, $label_kaprodi, array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'C' . $r . ':E' . $r, $kabag, array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, 'Telp. : ' . (isset($data['telp']) ? $data['telp'] : ''), array('vertikal' => 'tengah'));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        // Baris 4: Tanggal Audit
        $this->_kotakIsi($sheet, 'A' . $r . ':B' . $r, 'Tanggal Audit', array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'C' . $r . ':H' . $r, $tgl, array('vertikal' => 'tengah'));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        // Baris 5: Ketua Auditor & Perguruan tinggi
        $this->_kotakIsi($sheet, 'A' . $r . ':B' . $r, 'Ketua Auditor', array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'C' . $r . ':E' . $r, $ketua, array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, 'Perguruan tinggi : ' . $pt, array('vertikal' => 'tengah', 'bungkus' => TRUE));
        $sheet->getRowDimension($r)->setRowHeight(22);
        $r++;

        // Baris 6: Anggota Auditor
        $tinggi_anggota = max(20, count($anggota_teks) * 16);
        $this->_kotakIsi($sheet, 'A' . $r . ':B' . $r, 'Anggota Auditor', array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'C' . $r . ':H' . $r, $anggota_str, array('vertikal' => 'tengah', 'bungkus' => TRUE));
        $sheet->getRowDimension($r)->setRowHeight($tinggi_anggota);
        $r++;

        // Baris 7: Tanda Tangan (2 baris gabungan)
        $r2 = $r + 1;
        $this->_kotakIsi($sheet, 'A' . $r . ':B' . $r2, "Tanda Tangan\nAuditor", array('vertikal' => 'atas', 'bungkus' => TRUE));
        $this->_kotakIsi($sheet, 'C' . $r . ':D' . $r2, '', array('vertikal' => 'tengah'));
        $this->_kotakIsi($sheet, 'E' . $r . ':F' . $r2, $label_ttd_kaprodi, array('vertikal' => 'atas', 'bungkus' => TRUE));
        $this->_kotakIsi($sheet, 'G' . $r . ':H' . $r2, '', array('vertikal' => 'tengah'));
        $sheet->getRowDimension($r)->setRowHeight(24);
        $sheet->getRowDimension($r2)->setRowHeight(24);
        $r += 2;

        return $r;
    }

    /**
     * II. TUJUAN AUDIT
     */
    private function _renderTujuan($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'II.  TUJUAN AUDIT:', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(18);
        $r++;

        // Subtitle italic biru/abu
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'Beri tanda √ sesuai yang dikerjakan.', array('miring' => TRUE, 'ukuran' => 9, 'warna' => '2563EB'));
        $sheet->getRowDimension($r)->setRowHeight(16);
        $r++;

        $tujuan_list = !empty($data['tujuan_items']) && is_array($data['tujuan_items']) ? $data['tujuan_items'] : $this->tujuan_baku;
        $centang = isset($data['tujuan']) && is_array($data['tujuan']) ? $data['tujuan'] : range(0, count($tujuan_list) - 1);

        foreach ($tujuan_list as $idx => $teks) {
            $is_centang = in_array($idx, $centang);
            $teks_butir = ($idx + 1) . '. ' . $teks;

            $this->_kotakIsi($sheet, 'A' . $r . ':G' . $r, $teks_butir, array('vertikal' => 'tengah', 'bungkus' => TRUE));
            $this->_kotakIsi($sheet, 'H' . $r, $is_centang ? '✓' : '', array('rata' => 'tengah', 'vertikal' => 'tengah', 'tebal' => TRUE, 'ukuran' => 11));
            
            $tinggi = max(20, $this->_hitungTinggi($teks_butir, 80));
            $sheet->getRowDimension($r)->setRowHeight($tinggi);
            $r++;
        }

        return $r;
    }

    /**
     * III. LINGKUP AUDIT (Numbered list tanpa border tabel)
     */
    private function _renderLingkup($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'III. LINGKUP AUDIT:', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        $lingkup = isset($data['lingkup']) && is_array($data['lingkup']) ? $data['lingkup'] : array();
        if (empty($lingkup)) {
            $lingkup = array('Standar mutu program studi/unit');
        }

        foreach ($lingkup as $idx => $standar) {
            $standar = $this->_bersih($standar);
            $this->_tulis($sheet, 'B' . $r . ':H' . $r, ($idx + 1) . '.  ' . $standar, array('vertikal' => 'tengah'));
            $sheet->getRowDimension($r)->setRowHeight(18);
            $r++;
        }

        return $r;
    }

    /**
     * IV. JADWAL AUDIT
     */
    private function _renderJadwal($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'IV. JADWAL AUDIT:', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        $tgl_jadwal = isset($data['tanggal_audit']) ? $this->_tglIndo($data['tanggal_audit'], TRUE) : '';
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'Hari/Tanggal audit: ' . $tgl_jadwal, array('vertikal' => 'tengah'));
        $sheet->getRowDimension($r)->setRowHeight(18);
        $r++;

        // Header Tabel
        $header_gaya = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'latar' => 'F3F4F6');
        $this->_kotakIsi($sheet, 'A' . $r, 'No', $header_gaya);
        $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, 'Jam', $header_gaya);
        $this->_kotakIsi($sheet, 'D' . $r . ':H' . $r, 'Kegiatan Audit', array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'latar' => 'F3F4F6'));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        $jadwal = !empty($data['jadwal']) && is_array($data['jadwal']) ? $data['jadwal'] : $this->jadwal_baku;
        foreach ($jadwal as $idx => $j) {
            $jam = isset($j['jam']) ? $j['jam'] : '';
            $keg = isset($j['kegiatan']) ? $j['kegiatan'] : '';

            $this->_kotakIsi($sheet, 'A' . $r, (string) ($idx + 1), array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, $jam, array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'D' . $r . ':H' . $r, $keg, array('vertikal' => 'tengah'));
            $sheet->getRowDimension($r)->setRowHeight(20);
            $r++;
        }

        return $r;
    }

    /**
     * V. TEMUAN AUDIT (Dinamis semua temuan)
     */
    private function _renderTemuan($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'V.  TEMUAN AUDIT:', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(22);
        $r++;

        // Header Tabel
        $header_gaya = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE, 'latar' => 'F3F4F6');
        $this->_kotakIsi($sheet, 'A' . $r, "No", $header_gaya);
        $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, "Kategori\nTemuan\nKTS/OB", $header_gaya);
        $this->_kotakIsi($sheet, 'D' . $r . ':E' . $r, "Indikator (butir mutu)", $header_gaya);
        $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, "Pernyataan", $header_gaya);
        $sheet->getRowDimension($r)->setRowHeight(36);
        $r++;

        $temuan = isset($data['temuan']) && is_array($data['temuan']) ? $data['temuan'] : array();
        if (empty($temuan)) {
            $this->_kotakIsi($sheet, 'A' . $r, '1', array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, '-', array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'D' . $r . ':E' . $r, 'Tidak ada temuan KTS/OB.', array('vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, '-', array('vertikal' => 'tengah'));
            $sheet->getRowDimension($r)->setRowHeight(24);
            $r++;
            return $r;
        }

        foreach ($temuan as $idx => $t) {
            $kat = isset($t['kategori']) ? $this->_kategoriLabel($t['kategori']) : '';
            $ind = isset($t['indikator']) ? $this->_bersih($t['indikator']) : '';
            $per = isset($t['pernyataan']) ? $this->_bersih($t['pernyataan']) : '';

            $this->_kotakIsi($sheet, 'A' . $r, (string) ($idx + 1) . '.', array('rata' => 'tengah', 'vertikal' => 'atas'));
            $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, $kat, array('vertikal' => 'atas', 'bungkus' => TRUE));
            $this->_kotakIsi($sheet, 'D' . $r . ':E' . $r, $ind, array('vertikal' => 'atas', 'bungkus' => TRUE));
            $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, $per, array('vertikal' => 'atas', 'bungkus' => TRUE));

            $tinggi = max(36, $this->_hitungTinggi($ind, 28), $this->_hitungTinggi($per, 27));
            $sheet->getRowDimension($r)->setRowHeight($tinggi);
            $r++;
        }

        return $r;
    }

    /**
     * VI. PELUANG PENINGKATAN
     */
    private function _renderPeluang($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'VI. PELUANG PENINGKATAN', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(22);
        $r++;

        // Header Tabel
        $header_gaya = array('tebal' => TRUE, 'rata' => 'tengah', 'vertikal' => 'tengah', 'bungkus' => TRUE, 'latar' => 'F3F4F6');
        $this->_kotakIsi($sheet, 'A' . $r, 'No', $header_gaya);
        $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, 'Aspek/Bidang', $header_gaya);
        $this->_kotakIsi($sheet, 'D' . $r . ':E' . $r, 'Kelebihan', $header_gaya);
        $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, 'Peluang untuk peningkatan', $header_gaya);
        $sheet->getRowDimension($r)->setRowHeight(24);
        $r++;

        $peluang = isset($data['peluang']) && is_array($data['peluang']) ? $data['peluang'] : array();
        if (empty($peluang)) {
            $this->_kotakIsi($sheet, 'A' . $r, '1', array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, '-', array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'D' . $r . ':E' . $r, '-', array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, '-', array('rata' => 'tengah', 'vertikal' => 'tengah'));
            $sheet->getRowDimension($r)->setRowHeight(24);
            $r++;
            return $r;
        }

        foreach ($peluang as $idx => $p) {
            $aspek = isset($p['aspek']) ? $this->_bersih($p['aspek']) : '';
            $kelebihan = isset($p['kelebihan']) ? $this->_bersih($p['kelebihan']) : '';
            $peluang_teks = isset($p['peluang']) ? $this->_bersih($p['peluang']) : '';

            $this->_kotakIsi($sheet, 'A' . $r, (string) ($idx + 1) . '.', array('rata' => 'tengah', 'vertikal' => 'atas'));
            $this->_kotakIsi($sheet, 'B' . $r . ':C' . $r, $aspek, array('vertikal' => 'atas', 'bungkus' => TRUE));
            $this->_kotakIsi($sheet, 'D' . $r . ':E' . $r, $kelebihan, array('vertikal' => 'atas', 'bungkus' => TRUE));
            $this->_kotakIsi($sheet, 'F' . $r . ':H' . $r, $peluang_teks, array('vertikal' => 'atas', 'bungkus' => TRUE));

            $tinggi = max(32, $this->_hitungTinggi($aspek, 27), $this->_hitungTinggi($kelebihan, 28), $this->_hitungTinggi($peluang_teks, 27));
            $sheet->getRowDimension($r)->setRowHeight($tinggi);
            $r++;
        }

        return $r;
    }

    /**
     * VII. KESIMPULAN AUDIT
     */
    private function _renderKesimpulan($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'VII. KESIMPULAN AUDIT', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(22);
        $r++;

        $k = isset($data['kesimpulan']) && is_array($data['kesimpulan']) ? $data['kesimpulan'] : array();
        $k1 = isset($k['kesimpulan1']) ? $k['kesimpulan1'] : '';
        $k1_lain = isset($k['kesimpulan1_lainnya']) ? $this->_bersih($k['kesimpulan1_lainnya']) : '';
        $k2 = isset($k['kesimpulan2']) ? $k['kesimpulan2'] : '';
        $k2_lain = isset($k['kesimpulan2_lainnya']) ? $this->_bersih($k['kesimpulan2_lainnya']) : '';

        // Hitung rekapitulasi temuan (Mayor, Minor, Observasi)
        $temuan = isset($data['temuan']) && is_array($data['temuan']) ? $data['temuan'] : array();
        $c_mayor = 0; $c_minor = 0; $c_ob = 0;
        foreach ($temuan as $t) {
            $kat = isset($t['kategori']) ? strtoupper($t['kategori']) : '';
            if (strpos($kat, 'MAYOR') !== FALSE) {
                $c_mayor++;
            } elseif (strpos($kat, 'MINOR') !== FALSE) {
                $c_minor++;
            } else {
                $c_ob++;
            }
        }

        // Pertanyaan 1
        $p1 = "1. Sistem dokumen cukup lengkap dan terstruktur untuk mendukung pelaksanaan\n   Sistem penjamin Mutu Internal.";
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, $p1, array('vertikal' => 'tengah', 'bungkus' => TRUE));
        $sheet->getRowDimension($r)->setRowHeight(30);
        $r++;

        $r = $this->_renderOpsiKotak($sheet, $r, $k1, $k1_lain);

        // Pertanyaan 2
        $p2 = "2. Program studi/unit telah menjalankan Sistem Penjamin Mutu Internal secara\n   konsisten dan berkelanjutan.";
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, $p2, array('vertikal' => 'tengah', 'bungkus' => TRUE));
        $sheet->getRowDimension($r)->setRowHeight(30);
        $r++;

        $r = $this->_renderOpsiKotak($sheet, $r, $k2, $k2_lain);

        // Pertanyaan 3: Rekap Temuan
        $teks_k3 = "3. Temuan pada periode audit ini adalah: ({$c_mayor}) mayor, ({$c_minor}) minor, ({$c_ob}) Observasi";
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, $teks_k3, array('vertikal' => 'tengah', 'bungkus' => TRUE));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        return $r;
    }

    /**
     * Render kotak checklist untuk kesimpulan (Ya, Tidak, Lainnya)
     */
    private function _renderOpsiKotak($sheet, $r, $jawaban, $keterangan_lain = '') {
        $opsi = array('Ya', 'Tidak', 'Lainnya');
        foreach ($opsi as $item) {
            $checked = ($jawaban === $item);
            // Kotak kecil bergaris di kolom B
            $this->_kotakIsi($sheet, 'B' . $r, $checked ? '√' : '', array('rata' => 'tengah', 'vertikal' => 'tengah', 'tebal' => TRUE));
            
            // Teks di sampingnya pada kolom C:H
            $label = $item;
            if ($item === 'Lainnya') {
                $label = 'Lainnya, Sebutkan ' . ($keterangan_lain !== '' ? $keterangan_lain : '............');
            }
            $this->_tulis($sheet, 'C' . $r . ':H' . $r, ' ' . $label, array('vertikal' => 'tengah'));
            
            $sheet->getRowDimension($r)->setRowHeight(18);
            $r++;
        }
        return $r;
    }

    /**
     * VIII. LAMPIRAN AUDIT (Numbered list tanpa border tabel)
     */
    private function _renderLampiran($sheet, $data, $r) {
        $this->_tulis($sheet, 'A' . $r . ':H' . $r, 'VIII. LAMPIRAN AUDIT:', array('tebal' => TRUE, 'ukuran' => 11));
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;

        $lampiran = !empty($data['lampiran']) && is_array($data['lampiran']) ? $data['lampiran'] : $this->lampiran_baku;
        foreach ($lampiran as $idx => $teks) {
            $this->_tulis($sheet, 'B' . $r . ':H' . $r, ($idx + 1) . '.  ' . $teks, array('vertikal' => 'tengah'));
            $sheet->getRowDimension($r)->setRowHeight(18);
            $r++;
        }

        return $r;
    }

    /* ==================================================================
       PEMBANTU FORMAT & CETAK
       ================================================================== */

    /**
     * Tulis sel tanpa kotak border tabel luar.
     */
    private function _tulis($sheet, $rentang, $nilai, $gaya = array()) {
        if (strpos($rentang, ':') !== FALSE) {
            $sheet->mergeCells($rentang);
        }
        $sel = $this->awal($rentang);
        if ($nilai !== NULL && $nilai !== '') {
            $sheet->setCellValueExplicit($sel, (string) $nilai, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $this->_gaya($sheet, $rentang, $gaya);
    }

    /**
     * Tulis sel gabungan dengan border kotak (allborders).
     */
    private function _kotakIsi($sheet, $rentang, $nilai, $gaya = array()) {
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
                'color' => array('rgb' => isset($gaya['latar']) ? $gaya['latar'] : 'FFFFFF'),
            ),
        ));

        if ($nilai !== NULL && $nilai !== '') {
            $sel = $this->awal($rentang);
            $sheet->setCellValueExplicit($sel, (string) $nilai, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $this->_gaya($sheet, $rentang, $gaya);
    }

    /**
     * Terapkan gaya font & alignment.
     */
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
        } elseif (isset($gaya['rata']) && $gaya['rata'] === 'kanan') {
            $atur->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        } else {
            $atur->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }

        $huruf = $sheet->getStyle($rentang)->getFont();
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

    /**
     * Format cetak A4 Portrait
     */
    private function _aturCetak($sheet, $total_baris) {
        $atur = $sheet->getPageSetup();
        $atur->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $atur->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $atur->setFitToPage(TRUE);
        $atur->setFitToWidth(1);
        $atur->setFitToHeight(0); // Biarkan halaman mengalir vertikal sesuai panjang isi
        $atur->setHorizontalCentered(TRUE);
        $atur->setPrintArea('A1:H' . $total_baris);

        $sheet->setShowGridlines(FALSE);

        $margin = $sheet->getPageMargins();
        $margin->setLeft(0.5);
        $margin->setRight(0.5);
        $margin->setTop(0.5);
        $margin->setBottom(0.5);
        $margin->setHeader(0.3);
        $margin->setFooter(0.3);
    }

    /**
     * Hitung perkiraan tinggi baris teks.
     */
    private function _hitungTinggi($teks, $lebar_kolom) {
        $teks = (string) $teks;
        if ($teks === '') {
            return 20;
        }
        $per_baris = max(8, (int) floor($lebar_kolom * 0.95));
        $lines = 0;
        foreach (explode("\n", $teks) as $b) {
            $lines += max(1, (int) ceil(strlen($b) / $per_baris));
        }
        return max(20, $lines * 14 + 6);
    }

    /**
     * Format teks kategori temuan (e.g. KTS Observasi, Observasi).
     */
    private function _kategoriLabel($kategori) {
        $kat = strtoupper(trim((string) $kategori));
        if ($kat === 'OB' || $kat === 'OBSERVASI') {
            return 'Observasi';
        }
        if ($kat === 'KTS OBSERVASI' || $kat === 'KTS OB') {
            return "KTS\nObservasi";
        }
        if ($kat === 'TS MINOR' || $kat === 'MINOR') {
            return 'TS Minor';
        }
        if ($kat === 'TS MAYOR' || $kat === 'MAYOR') {
            return 'TS Mayor';
        }
        return $kategori;
    }

    /**
     * Format tanggal Indonesia (contoh: 10 November 2025).
     */
    private function _tglIndo($dateStr, $withDay = FALSE) {
        if (empty($dateStr)) return '';
        $time = is_numeric($dateStr) ? (int) $dateStr : strtotime($dateStr);
        if (!$time) {
            // Coba parse format dd/mm/yyyy
            if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})#', $dateStr, $m)) {
                $time = mktime(0, 0, 0, (int) $m[2], (int) $m[1], (int) $m[3]);
            } else {
                return (string) $dateStr;
            }
        }

        $bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        $hari = array(
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        );

        $tgl = date('j', $time) . ' ' . $bulan[(int) date('n', $time)] . ' ' . date('Y', $time);
        if ($withDay) {
            return $hari[date('l', $time)] . ', ' . $tgl;
        }
        return $tgl;
    }

    /** Sel pertama rentang (e.g. 'A1:H1' -> 'A1'). */
    private function awal($rentang) {
        $p = explode(':', $rentang);
        return $p[0];
    }

    /** Bersihkan string dari tag HTML & spasi berlebih. */
    private function _bersih($teks) {
        $teks = strip_tags((string) $teks);
        $teks = preg_replace('/[ \t]+/', ' ', $teks);
        return trim($teks);
    }

    /**
     * Kirim berkas Excel ke peramban.
     */
    public function kirim($excel, $nama_berkas) {
        $nama = trim(preg_replace('/[^A-Za-z0-9_\-]+/', '-', (string) $nama_berkas), '-');
        $nama = preg_replace('/-{2,}/', '-', $nama);
        if ($nama === '') {
            $nama = 'Laporan-AMI';
        }

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
}
