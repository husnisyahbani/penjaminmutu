<?php
/* Jalankan: php tests/laporan_ami_test.php [lokasi-contoh.xlsx] */
define('BASEPATH', dirname(__DIR__) . '/system/');
define('APPPATH', dirname(__DIR__) . '/application/');
require APPPATH . 'libraries/Laporanami.php';
function periksa($ok, $pesan) {
    if (!$ok) { fwrite(STDERR, "GAGAL: $pesan\n"); exit(1); }
}
$data = array(
    'unit' => 'Unit Contoh', 'alamat' => '', 'kabag' => 'Auditee Contoh',
    'tanggal_audit' => '12/04/2025', 'ketua_auditor' => 'Ketua Contoh',
    'anggota' => array('Anggota Contoh'), 'tujuan' => array(1),
    'tujuan_lain' => 'Tujuan audit yang tercatat', 'lingkup' => array('Standar mutu contoh'),
    'temuan' => array(
        array('kategori'=>'OB','indikator'=>'Butir 1','pernyataan'=>'Hasil 1'),
        array('kategori'=>'TS MINOR','indikator'=>'Butir 2','pernyataan'=>'Hasil 2'),
        array('kategori'=>'OB','indikator'=>'Butir 3','pernyataan'=>'Hasil 3'),
        array('kategori'=>'TS MAYOR','indikator'=>'Butir 4','pernyataan'=>'Hasil 4')
    ),
    'peluang' => array(array('aspek'=>'Standar','kelebihan'=>'Baik','peluang'=>'Perbaikan')),
    'kesimpulan' => array('kesimpulan1'=>'Lainnya','kesimpulan1_lainnya'=>'Keterangan lain',
        'kesimpulan2'=>'Ya','kesimpulan3'=>'Minor')
);
$laporan = new Laporanami();
$excel = $laporan->buat($data);
$s = $excel->getActiveSheet();
periksa($s->getTitle() === 'Laporan AMI', 'Nama lembar');
periksa($s->getPageSetup()->getPrintArea() === 'A1:H79', 'Area cetak');
periksa($s->getPageSetup()->getFitToWidth() == 1 && $s->getPageSetup()->getFitToHeight() == 0, 'Skala cetak');
periksa($s->getCell('C5')->getValue() === 'Unit Contoh', 'Unit');
periksa($s->getCell('G60')->getValue() === '☒ Lainnya sebutkan', 'Pilihan Lainnya');
periksa($s->getCell('A61')->getValue() === 'Keterangan lain', 'Keterangan Lainnya');
periksa($s->getCell('C64')->getValue() === '☒ Ya', 'Pilihan Ya');
periksa($s->getCell('E67')->getValue() === '☒ Minor', 'Kategori');
periksa(strpos($s->getCell('D48')->getValue(), 'Butir 4') !== FALSE, 'Temuan tambahan tidak hilang');
periksa($s->getCell('G60')->getDataValidation()->getFormula1() === '"☐ Lainnya sebutkan,☒ Lainnya sebutkan"', 'Daftar pilihan Lainnya');
periksa($s->getCell('A70')->getDataValidation()->getType() === PHPExcel_Cell_DataValidation::TYPE_LIST, 'Pilihan lampiran');
if (isset($argv[1])) PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($argv[1]);
echo "Laporan AMI: semua pemeriksaan berhasil.\n";
