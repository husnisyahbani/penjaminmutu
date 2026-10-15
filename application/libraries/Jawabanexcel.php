<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Format jawaban auditee dalam bentuk berkas Excel (memakai PHPExcel yang
 * sudah tersedia di application/third_party).
 *
 * Dipakai tombol "Isi lewat Excel" pada halaman
 * Auditee -> Daftar Audit -> (detail):
 *
 *   - unduhFormat()  mengirim berkas format yang barisnya disusun mengikuti
 *                    formulir audit (pertanyaan + butir lingkup), lengkap
 *                    dengan jawaban dan tautan lampiran yang sudah tersimpan,
 *                    sehingga auditee dapat melanjutkan pengisian;
 *   - baca()         membaca kembali berkas yang diunggah menjadi daftar
 *                    baris siap olah (kunci: id, pertanyaan, butir,
 *                    jawaban, tautan).
 *
 * Kolom pada berkas (urutan baku, nama kolom dikenali juga bila dipindah):
 *   Id Butir | Pertanyaan | Butir Lingkup | Jawaban | Link Lampiran
 *
 * Berkas format ditulis sebagai Excel 97-2003 (.xls) supaya tidak bergantung
 * pada ekstensi Zip di server (penulis Excel5 tidak membutuhkannya), sama
 * seperti unduhan hasil audit pada menu admin. Berkas yang DITERIMA boleh
 * .xls, .xlsx, maupun .csv - jenisnya dikenali otomatis.
 */
class Jawabanexcel {

    /** Nama kolom baku pada berkas format. */
    var $kolom = array('Id Butir', 'Pertanyaan', 'Butir Lingkup', 'Jawaban', 'Link Lampiran');

    /** Nomor baris kepala kolom (baris 1 judul, baris 2 petunjuk). */
    var $baris_kepala = 3;

    /** Lebar kolom (A..E). */
    var $lebar = array(10, 42, 52, 58, 38);

    function __construct() {
        require_once APPPATH . 'third_party/PHPExcel.php';
        require_once APPPATH . 'third_party/PHPExcel/IOFactory.php';
    }

    /* ==================================================================
       UNDUH BERKAS FORMAT
       ================================================================== */

    /**
     * Susun berkas format jawaban lalu kirim ke peramban (langsung keluar).
     *
     * @param string $nama_berkas nama berkas tanpa ekstensi
     * @param string $judul       judul pada baris pertama (nama formulir)
     * @param array  $baris       daftar baris; tiap baris berisi kunci:
     *                            id, pertanyaan, butir, jawaban, tautan
     */
    public function unduhFormat($nama_berkas, $judul, $baris) {
        $excel = new PHPExcel();
        $sheet = $excel->setActiveSheetIndex(0);
        $sheet->setTitle('Jawaban');

        $jumlah   = count($this->kolom);
        $terakhir = PHPExcel_Cell::stringFromColumnIndex($jumlah - 1);

        /* ---------------- baris 1: judul ---------------- */
        $sheet->setCellValue('A1', (string) $judul);
        $sheet->mergeCells('A1:' . $terakhir . '1');
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(13);

        /* ---------------- baris 2: petunjuk ---------------- */
        $petunjuk = 'Isi kolom "Jawaban" pada setiap butir lingkup (wajib). '
            . 'Lampiran cukup berupa tautan (link) pada kolom "Link Lampiran"; '
            . 'bila lebih dari satu, pisahkan dengan koma. '
            . 'Kolom "Id Butir" dan "Pertanyaan" jangan diubah.';
        $sheet->setCellValue('A2', $petunjuk);
        $sheet->mergeCells('A2:' . $terakhir . '2');
        $sheet->getStyle('A2')->getAlignment()
            ->setWrapText(TRUE)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $sheet->getRowDimension(2)->setRowHeight(46);

        /* ---------------- baris 3: kepala kolom ---------------- */
        $kepala = $this->baris_kepala;
        for ($c = 0; $c < $jumlah; $c++) {
            $sheet->setCellValueExplicitByColumnAndRow(
                $c, $kepala, $this->kolom[$c], PHPExcel_Cell_DataType::TYPE_STRING
            );
        }

        $rentang = 'A' . $kepala . ':' . $terakhir . $kepala;
        $sheet->getStyle($rentang)->getFont()->setBold(TRUE);
        $sheet->getStyle($rentang)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE8EAF6');
        $sheet->getStyle($rentang)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        /* ---------------- isi ---------------- */
        $no = $kepala;
        $kunci = array('id', 'pertanyaan', 'butir', 'jawaban', 'tautan');
        foreach ((array) $baris as $b) {
            $no++;
            for ($c = 0; $c < $jumlah; $c++) {
                /* TYPE_STRING supaya teks yang diawali "=" tidak dianggap
                   rumus dan angka di kolom Id tidak diubah formatnya. */
                $sheet->setCellValueExplicitByColumnAndRow(
                    $c, $no, $this->nilai($b, $kunci[$c]), PHPExcel_Cell_DataType::TYPE_STRING
                );
            }
        }

        /* ---------------- tampilan ---------------- */
        for ($c = 0; $c < $jumlah; $c++) {
            $sheet->getColumnDimensionByColumn($c)->setWidth($this->lebar[$c]);
        }

        $isi_akhir = max($no, $kepala);
        $sheet->getStyle('B' . $kepala . ':' . $terakhir . $isi_akhir)
            ->getAlignment()
            ->setWrapText(TRUE)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $sheet->getStyle('A' . $kepala . ':' . $terakhir . $isi_akhir)
            ->applyFromArray(array(
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                ),
            ));
        $sheet->freezePane('A' . ($kepala + 1));

        /* ---------------- kirim ---------------- */
        $nama = preg_replace('/[^A-Za-z0-9_\-]/', '-', (string) $nama_berkas);
        if ($nama === '') {
            $nama = 'Format-Jawaban';
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
       BACA BERKAS YANG DIUNGGAH
       ================================================================== */

    /**
     * Baca berkas jawaban yang diunggah.
     *
     * Baris judul dan petunjuk dilewati: kepala kolom dicari berdasar nama
     * kolom, sehingga berkas yang kolomnya dipindah tetap terbaca.
     *
     * @param  string $berkas path lengkap berkas sementara
     * @return array  daftar baris; tiap baris: baris, id, pertanyaan, butir,
     *                jawaban, tautan
     * @throws Exception bila berkas tidak dapat dibaca
     */
    public function baca($berkas) {
        if (!is_file($berkas)) {
            throw new Exception('Berkas tidak ditemukan di server.');
        }

        $jenis    = PHPExcel_IOFactory::identify($berkas);
        $pembaca  = PHPExcel_IOFactory::createReader($jenis);
        $pembaca->setReadDataOnly(TRUE);
        $excel    = $pembaca->load($berkas);
        $sheet    = $excel->getSheet(0);
        if (!$sheet) {
            throw new Exception('Berkas tidak memiliki lembar kerja.');
        }

        $mentah = $sheet->toArray(NULL, TRUE, TRUE, TRUE);
        $excel->disconnectWorksheets();
        unset($excel);

        /* --------- cari baris kepala kolom --------- */
        $kepala = 0;
        foreach ($mentah as $no => $sel) {
            $nilai = array();
            foreach ((array) $sel as $v) {
                $nilai[] = strtolower(trim((string) $v));
            }
            if (in_array('id butir', $nilai)
                    || (in_array('jawaban', $nilai) && in_array('link lampiran', $nilai))) {
                $kepala = (int) $no;
                break;
            }
        }

        if ($kepala < 1) {
            throw new Exception('Baris judul kolom tidak ditemukan. Unduh format dari aplikasi, lalu isi jawaban pada kolom yang tersedia.');
        }

        /* --------- petakan nama kolom -> nomor kolom --------- */
        $judul_kolom = array();
        foreach ((array) $mentah[$kepala] as $v) {
            $judul_kolom[] = strtolower(trim((string) $v));
        }

        $kandidat = array(
            'id'         => array('id butir', 'id', 'id butir lingkup'),
            'pertanyaan' => array('pertanyaan', 'pertanyaan audit'),
            'butir'      => array('butir lingkup', 'butir'),
            'jawaban'    => array('jawaban', 'jawaban auditee'),
            'tautan'     => array('link lampiran', 'lampiran', 'tautan', 'link'),
        );

        $kolom = array();
        foreach ($kandidat as $kunci => $nama_mungkin) {
            $kolom[$kunci] = NULL;
            foreach ($nama_mungkin as $nama) {
                $pos = array_search($nama, $judul_kolom);
                if ($pos !== FALSE) {
                    $kolom[$kunci] = (int) $pos;
                    break;
                }
            }
        }

        if ($kolom['id'] === NULL || $kolom['jawaban'] === NULL) {
            throw new Exception('Kolom "Id Butir" dan "Jawaban" harus ada pada berkas. Unduh format dari aplikasi bila ragu.');
        }

        /* --------- kumpulkan baris isi --------- */
        $hasil = array();
        foreach ($mentah as $no => $sel) {
            if ((int) $no <= $kepala) {
                continue;
            }

            $sel = array_values((array) $sel);
            $baris = array(
                'baris'      => (int) $no,
                'id'         => $this->sel($sel, $kolom['id']),
                'pertanyaan' => $this->sel($sel, $kolom['pertanyaan']),
                'butir'      => $this->sel($sel, $kolom['butir']),
                'jawaban'    => $this->sel($sel, $kolom['jawaban']),
                'tautan'     => $this->sel($sel, $kolom['tautan']),
            );

            /* Baris kosong dilewati (mis. sisa baris kosong di Excel). */
            $ada = $baris['id'] . $baris['butir'] . $baris['jawaban'] . $baris['tautan'];
            if (trim($ada) === '') {
                continue;
            }

            $hasil[] = $baris;
        }

        if (empty($hasil)) {
            throw new Exception('Belum ada jawaban yang diisi pada berkas.');
        }

        return $hasil;
    }

    /* ==================================================================
       BANTUAN
       ================================================================== */

    /** Ambil nilai satu kolom pada baris, aman bila kolomnya tidak ada. */
    private function sel($sel, $pos) {
        if ($pos === NULL || !isset($sel[$pos])) {
            return '';
        }
        return trim((string) $sel[$pos]);
    }

    /** Ambil nilai berkunci dari satu baris data (untuk unduhan). */
    private function nilai($baris, $kunci) {
        if (is_array($baris) && isset($baris[$kunci])) {
            return (string) $baris[$kunci];
        }
        return '';
    }
}
