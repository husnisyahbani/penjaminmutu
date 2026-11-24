<?php
/* Jalankan: php tests/kesimpulan_ami_test.php admin|auditor */
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);
class MY_Controller {}
class UjiMutu {
    function getAuditById($id) {
        return (int)$id === 1 ? array('audit_id'=>1, 'auditor_id'=>10, 'audit_status'=>'SELESAI')
             : ((int)$id === 2 ? array('audit_id'=>2, 'auditor_id'=>10, 'audit_status'=>'PROSES') : NULL);
    }
}
class UjiSesi { public $id = 10; function userdata($k) { return $k === 'users_id' ? $this->id : NULL; } }
class UjiTim { function daftar($id) { return array(array('users_id'=>11, 'tim_peran'=>'ANGGOTA')); } }
class UjiInput { public $data = array(); function post($k) { return isset($this->data[$k]) ? $this->data[$k] : NULL; } }
class UjiKesimpulan {
    public $tersedia = TRUE; public $data = array();
    function siap() { return $this->tersedia; }
    function ambil($id) { return isset($this->data[$id]) ? $this->data[$id] : NULL; }
    function sudahDiisi($id) { return !!$this->ambil($id); }
    function simpan($id, $data) { $this->data[$id] = $data; return TRUE; }
}
class UjiLoader { function model($nama, $alias) {} }
function periksa($ok, $pesan) { if (!$ok) { fwrite(STDERR, "GAGAL: $pesan\n"); exit(1); } }
function panggil($obj, $method, $arg = NULL) { ob_start(); if ($arg === NULL) $obj->$method(); else $obj->$method($arg); return json_decode(ob_get_clean(), TRUE); }
$mod = isset($argv[1]) ? $argv[1] : 'admin';
periksa(in_array($mod, array('admin', 'auditor'), TRUE), 'modul');
require dirname(__DIR__) . '/application/modules/' . $mod . '/controllers/Daftaraudit.php';
$ref = new ReflectionClass('Daftaraudit');
$c = $ref->newInstanceWithoutConstructor();
$c->load = new UjiLoader(); $c->mutu = new UjiMutu(); $c->input = new UjiInput();
$c->session = new UjiSesi(); $c->timaudit = new UjiTim(); $c->kesimpulan = new UjiKesimpulan();
periksa(panggil($c, 'kesimpulan', 1)['sudah'] === FALSE, 'belum diisi');
periksa(panggil($c, 'kesimpulan', 2)['status'] === FALSE, 'audit belum selesai ditolak');
periksa(panggil($c, 'kesimpulan', 99)['status'] === FALSE, 'audit tidak ada ditolak');
$c->kesimpulan->tersedia = FALSE;
periksa(panggil($c, 'kesimpulan', 1)['status'] === FALSE, 'tabel wajib tersedia');
$c->kesimpulan->tersedia = TRUE;
$c->input->data = array('audit_id'=>1, 'kesimpulan1'=>'Lainnya', 'kesimpulan2'=>'Ya', 'kesimpulan3'=>'Minor');
periksa(panggil($c, 'simpankesimpulan')['status'] === FALSE, 'keterangan lainnya wajib');
$c->input->data['kesimpulan1_lainnya'] = 'Keterangan';
periksa(panggil($c, 'simpankesimpulan')['status'] === TRUE, 'simpan jawaban');
$r = panggil($c, 'kesimpulan', 1);
periksa($r['sudah'] && $r['kesimpulan']['kesimpulan1_lainnya'] === 'Keterangan', 'jawaban tersimpan');
if ($mod === 'auditor') {
    $c->session->id = 11; periksa(panggil($c, 'kesimpulan', 1)['status'] === TRUE, 'anggota tim');
    $c->session->id = 12; periksa(panggil($c, 'kesimpulan', 1)['status'] === FALSE, 'auditor lain ditolak');
    periksa(panggil($c, 'simpankesimpulan')['status'] === FALSE, 'auditor lain tidak dapat mengubah');
}
echo "Kesimpulan $mod: semua pemeriksaan berhasil.\n";
