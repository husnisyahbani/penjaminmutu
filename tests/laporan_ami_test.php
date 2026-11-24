<?php
/* Jalankan: php tests/laporan_ami_test.php [lokasi-contoh.xlsx] */
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);
define('BASEPATH', dirname(__DIR__) . '/system/');
define('APPPATH', dirname(__DIR__) . '/application/');
require APPPATH . 'libraries/Laporanami.php';

function periksa($ok, $pesan) {
    if (!$ok) { fwrite(STDERR, "GAGAL: $pesan\n"); exit(1); }
}

$data = array(
    'unit' => 'Unit Contoh', 'alamat' => 'Jl. Contoh No. 1', 'kabag' => 'Auditee Contoh',
    'tanggal_audit' => '2025-04-12', 'ketua_auditor' => 'Ketua Contoh',
    'anggota' => array('Anggota Contoh 1', 'Anggota Contoh 2'),
    'tujuan' => array(1, 2, 3),
    'tujuan_lain' => 'Tujuan audit yang tercatat',
    'lingkup' => array('Standar Pendidikan', 'Standar Penelitian', 'Standar Pengabdian'),
    'temuan' => array(
        array('kategori'=>'OB','indikator'=>'Butir 1','pernyataan'=>'Hasil 1'),
        array('kategori'=>'TS MINOR','indikator'=>'Butir 2','pernyataan'=>'Hasil 2'),
        array('kategori'=>'OB','indikator'=>'Butir 3','pernyataan'=>'Hasil 3'),
        array('kategori'=>'TS MAYOR','indikator'=>'Butir 4','pernyataan'=>'Hasil 4')
    ),
    'peluang' => array(
        array('aspek'=>'Standar Pendidikan','kelebihan'=>'Kurikulum terstruktur','peluang'=>'Peningkatan sertifikasi dosen')
    ),
    'kesimpulan' => array(
        'kesimpulan1' => 'Memenuhi',
        'kesimpulan1_lainnya' => '',
        'kesimpulan2' => 'Ya',
        'kesimpulan3' => 'Minor'
    )
);

$laporan = new Laporanami();
$excel = $laporan->buat($data);
$sheet = $excel->getActiveSheet();

periksa($sheet->getTitle() === 'Laporan AMI', 'Nama lembar');
periksa($sheet->getCell('C4')->getValue() === 'Unit Contoh', 'Unit di C4');
periksa($sheet->getCell('C5')->getValue() === 'Jl. Contoh No. 1', 'Alamat di C5');
periksa($sheet->getCell('C6')->getValue() === 'Auditee Contoh', 'Kabag di C6');
periksa($sheet->getCell('C7')->getValue() === '12 April 2025', 'Tanggal audit di C7');
periksa($sheet->getCell('C8')->getValue() === 'Ketua Contoh', 'Ketua Auditor di C8');

// Periksa temuan
$foundTemuan = false;
$highestRow = $sheet->getHighestRow();
for ($r = 1; $r <= $highestRow; $r++) {
    if ($sheet->getCell("D$r")->getValue() === 'Butir 4') {
        $foundTemuan = true;
        break;
    }
}
periksa($foundTemuan, 'Semua temuan tercantum secara dinamis');

if (isset($argv[1])) {
    PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($argv[1]);
}
echo "Laporan AMI: semua pemeriksaan berhasil.\n";
