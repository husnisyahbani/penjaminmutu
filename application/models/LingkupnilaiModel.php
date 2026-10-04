<?php
/**
 * Catatan auditor per butir lingkup: "Kelebihan" dan "Peluang untuk
 * peningkatan" pada halaman Auditor -> Daftar Audit -> (detail).
 *
 * Data tersimpan pada tabel `lingkup_nilai` (mutu_lingkup_nilai),
 * dipisahkan per audit dan per butir lingkup, jadi tidak bergantung pada
 * ada/tidaknya baris tilik (mutu_auditjawabdetail).
 *
 * Bila tabelnya belum diimpor (lihat database/kelebihan_peluang.sql),
 * siap() bernilai FALSE dan halaman menonaktifkan bagian ini dengan aman.
 */
class LingkupnilaiModel extends CI_Model {

    /** Nama tabel tanpa prefix (prefix dibaca dari konfigurasi CI). */
    private $tabel = 'lingkup_nilai';

    function __construct() {
        parent::__construct();
    }

    /** Tabel catatan sudah tersedia? */
    public function siap() {
        return $this->db->table_exists($this->tabel);
    }

    /**
     * Catatan satu audit, dipetakan menurut butir lingkup.
     *
     * @param  int $audit_id
     * @return array lingkup_id => array('kelebihan' => ..., 'peluang' => ...)
     */
    public function peta($audit_id) {
        $peta = array();
        if (!$this->siap()) {
            return $peta;
        }

        $this->db->select('lingkup_id, kelebihan, peluang');
        $this->db->where('audit_id', (int) $audit_id);
        foreach ($this->db->get($this->tabel)->result_array() as $n) {
            $peta[(int) $n['lingkup_id']] = array(
                'kelebihan' => (string) $n['kelebihan'],
                'peluang'   => (string) $n['peluang'],
            );
        }

        return $peta;
    }

    /**
     * Simpan kelebihan & peluang peningkatan satu butir lingkup.
     * Baris dibuat bila belum ada, diperbarui bila sudah ada.
     *
     * @param  int    $audit_id
     * @param  int    $lingkup_id
     * @param  string $kelebihan
     * @param  string $peluang
     * @return bool
     */
    public function simpan($audit_id, $lingkup_id, $kelebihan, $peluang) {
        if (!$this->siap()) {
            return FALSE;
        }

        $audit_id   = (int) $audit_id;
        $lingkup_id = (int) $lingkup_id;
        if ($audit_id < 1 || $lingkup_id < 1) {
            return FALSE;
        }

        $users_id = $this->session->userdata('users_id');

        $this->db->where('audit_id', $audit_id);
        $this->db->where('lingkup_id', $lingkup_id);
        $ada = $this->db->get($this->tabel)->row_array();

        $isi = array(
            'kelebihan' => $kelebihan,
            'peluang'   => $peluang,
        );
        if (!empty($users_id)) {
            $isi['users_id'] = (int) $users_id;
        }

        if ($ada) {
            $this->db->where('nilai_id', (int) $ada['nilai_id']);
            return (bool) $this->db->update($this->tabel, $isi);
        }

        $isi['audit_id']      = $audit_id;
        $isi['lingkup_id']    = $lingkup_id;
        $isi['nilai_create']  = date('Y-m-d H:i:s');

        return (bool) $this->db->insert($this->tabel, $isi);
    }
}
