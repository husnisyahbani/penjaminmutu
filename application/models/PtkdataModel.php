<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Sumber data halaman PTK (Permintaan Tindakan Koreksi) untuk SEMUA peran.
 *
 * Satu baris tabel = satu butir tilik hasil penilaian auditor, yaitu baris
 * `mutu_auditjawabdetail` (dtjwb_pertanyaan / dtjwb_hasil / dtjwb_temuan /
 * dtjwb_catatan) yang menempel pada jawaban satu pertanyaan
 * (`mutu_auditjawab` -> audit_id + dtform_id).
 *
 * Kueri yang sama dipakai tiga halaman, dibedakan oleh PEMILIK baris:
 *   - 'auditee' : hanya audit milik auditee yang login (PTK auditee)
 *   - 'auditor' : hanya audit yang ditangani auditor yang login, jadi daftar
 *                 tilik yang tampil adalah tilik yang ia buat sendiri
 *                 (PTK auditor)
 *   - 'semua'   : seluruh data (PTK PPM/admin)
 *
 * Baris yang belum dinilai auditor (dtjwb_temuan kosong) tidak ditampilkan,
 * dan butir bernilai "S" hanya tampil pada halaman delik ($termasuk_s).
 *
 * Rencana koreksi disimpan per butir pada `auditjawabdetail.dtjwb_koreksi`
 * (lihat database/koreksi_butir.sql atau menu Migrasi). Kolom lama
 * `auditjawab.jwb_koreksi` sudah dihapus, jadi kolom `dtjwb_koreksi` wajib
 * tersedia agar rencana koreksi dapat disimpan.
 *
 * Catatan: model ini dipakai langsung oleh modul auditor dan admin, sedangkan
 * modul auditee meneruskannya lewat PtkModel miliknya sendiri.
 */
class PtkdataModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /* Bawaan mengikuti halaman PTK auditee (7 kolom). Halaman lain yang
       memakai susunan kolom berbeda menimpanya lewat aturKolom()/aturCari(),
       sehingga perilaku pencarian & pengurutan halaman auditee tidak berubah. */
    var $column_search = array('f.form_nama','dt.dtform_pertanyaan','dj.dtjwb_pertanyaan','dj.dtjwb_hasil','dj.dtjwb_temuan','dj.dtjwb_catatan');
    /* Urutan kolom tabel (No, Formulir, Butir, Hasil, Temuan, Catatan,
       Koreksi); yang null tidak dapat diurutkan. */
    var $column_order = array(null,'f.form_nama','dj.dtjwb_pertanyaan','dj.dtjwb_hasil','dj.dtjwb_temuan','dj.dtjwb_catatan',null);

    /**
     * Tetapkan urutan kolom DataTables pemanggil (null = tidak dapat
     * diurutkan). Dipakai halaman PTK auditor & PPM yang menambah kolom
     * Auditee dan Unit.
     */
    public function aturKolom($column_order) {
        if (is_array($column_order)) {
            $this->column_order = $column_order;
        }
        return $this;
    }

    /** Tetapkan kolom yang dicari DataTables pemanggil. */
    public function aturCari($column_search) {
        if (is_array($column_search)) {
            $this->column_search = $column_search;
        }
        return $this;
    }

    /**
     * Pemilik baris yang ditampilkan: 'auditee' | 'auditor' | 'semua'.
     * Bawaan 'auditee' supaya pemanggil lama tetap aman.
     */
    var $pemilik = 'auditee';

    /** Tetapkan pemilik baris (lihat dokblok kelas). */
    public function aturPemilik($pemilik) {
        if ($pemilik === 'auditee' || $pemilik === 'auditor' || $pemilik === 'semua') {
            $this->pemilik = $pemilik;
        }
        return $this;
    }

    /**
     * Saringan pemilik pada tabel audit.
     *
     * @param string $alias alias tabel mutu_audit pada kueri ('au' / 'ma')
     */
    private function _saringPemilik($alias) {
        if ($this->pemilik === 'semua') {
            return;
        }

        $kolom    = ($this->pemilik === 'auditor') ? 'auditor_id' : 'auditee_id';
        $users_id = $this->session->userdata('users_id');
        if (isset($users_id)) {
            $this->db->where($alias . '.' . $kolom, $users_id);
        }
    }

    /**
     * Kolom rencana koreksi: hanya `auditjawabdetail.dtjwb_koreksi`
     * (satu teks per butir). Kolom lama `auditjawab.jwb_koreksi` sudah
     * dihapus, jadi pastikan kolom ini ada (impor database/koreksi_butir.sql
     * atau menu PPM > Migrasi Lingkup > "Siapkan Kolom Koreksi Butir").
     */
    private function _koreksi_sql() {
        return 'dj.dtjwb_koreksi';
    }

    /** Sudah ada kolom koreksi per butir? */
    public function koreksiSiap() {
        return $this->db->field_exists('dtjwb_koreksi', 'auditjawabdetail');
    }

    private function _get_datatables_query($search, $ordering) {
        $i = 0;

        foreach ($this->column_search as $item) { // looping awal
            if ($search['value']) { // jika datatable mengirimkan pencarian dengan metode POST
                if ($i === 0) { // looping awal
                    $this->db->group_start();
                    $this->db->like($item, $search['value']);
                } else {
                    $this->db->or_like($item, $search['value']);
                }

                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($ordering[0]['column']) && isset($this->column_order[$ordering[0]['column']])
            && $this->column_order[$ordering[0]['column']] !== null) {
            $this->db->order_by($this->column_order[$ordering[0]['column']], $ordering[0]['dir']);
        } else {
            if ($this->db->field_exists('dtform_urut', 'detailform')) {
                $this->db->order_by('dt.dtform_urut', 'asc');
            }
            $this->db->order_by('dj.dtjwb_id', 'asc');
        }
    }

    /**
     * Kueri dasar daftar butir - SATU sumber data untuk halaman PTK dan
     * halaman delik (tabel mutu_auditjawabdetail).
     *
     * @param int|null $dtform_id  batasi ke satu pertanyaan (delik)
     * @param bool     $termasuk_s sertakan butir bernilai "S" (delik)
     * @param int|null $periode_id batasi ke satu periode audit (filter PTK);
     *                             kosong/NULL = semua periode
     */
    private function _query_ptk($dtform_id = NULL, $termasuk_s = FALSE, $periode_id = NULL) {
        $this->db->select('au.audit_id, au.audit_status, au.form_id');
        $this->db->select('au.auditee, au.unit, au.auditor');
        if ($this->db->field_exists('periode_id', 'audit')) {
            $this->db->select('au.periode_id');
        }
        $this->db->select('f.form_nama');
        $this->db->select('dt.dtform_id, dt.dtform_pertanyaan');
        if ($this->db->field_exists('dtform_urut', 'detailform')) {
            $this->db->select('dt.dtform_urut');
        }
        $this->db->select('dj.dtjwb_id, dj.dtjwb_pertanyaan, dj.dtjwb_hasil, dj.dtjwb_temuan, dj.dtjwb_catatan');
        $this->db->select('jb.jwb_id');
        /* Nama kunci disamakan dengan kolom tabel (lingkup_isi, jwb_*) supaya
           isi kolom sama persis pada halaman PTK, halaman delik, dan ekspor. */
        $this->db->select('dj.dtjwb_pertanyaan AS lingkup_isi', FALSE);
        $this->db->select('dj.dtjwb_hasil AS jwb_hasil', FALSE);
        $this->db->select('dj.dtjwb_temuan AS jwb_temuan', FALSE);
        $this->db->select('dj.dtjwb_catatan AS jwb_catatan', FALSE);
        if ($this->koreksiSiap()) {
            $this->db->select($this->_koreksi_sql() . ' AS jwb_koreksi', FALSE);
        }

        $this->db->from('auditjawabdetail dj');
        $this->db->join('auditjawab jb', 'jb.jwb_id = dj.jwb_id', 'inner');
        $this->db->join('audit au', 'au.audit_id = jb.audit_id', 'inner');
        $this->db->join('detailform dt', 'dt.dtform_id = jb.dtform_id', 'inner');
        $this->db->join('formulir f', 'f.form_id = au.form_id', 'left');

        /* Hanya butir yang sudah dinilai auditor. */
        $this->db->where('dj.dtjwb_temuan IS NOT NULL', NULL, FALSE);
        $this->db->where("TRIM(dj.dtjwb_temuan) <> ''", NULL, FALSE);

        /* PTK: tanpa nilai "S". Delik: "S" ikut tampil. */
        if (!$termasuk_s) {
            $this->db->where("UPPER(TRIM(dj.dtjwb_temuan)) <> 'S'", NULL, FALSE);
        }

        if (!empty($dtform_id)) {
            $this->db->where('jb.dtform_id', $dtform_id);
        }

        /* Saringan periode: periode_id melekat pada audit (mutu_audit).
           Kosong = semua periode. Dilewati bila kolomnya belum ada. */
        if (!empty($periode_id) && $this->db->field_exists('periode_id', 'audit')) {
            $this->db->where('au.periode_id', (int) $periode_id);
        }

        /* Saringan pemilik: auditee / auditor / semua. */
        $this->_saringPemilik('au');
    }

    /**
     * Daftar butir satu audit untuk halaman delik (tanpa paging DataTables).
     *
     * Kunci baris sama dengan halaman PTK: form_nama, lingkup_isi (butir),
     * jwb_hasil, jwb_temuan, jwb_catatan, jwb_koreksi, dtjwb_id, dtform_id.
     */
    public function daftar($audit_id, $dtform_id = NULL, $termasuk_s = FALSE) {
        $this->_query_ptk($dtform_id, $termasuk_s);
        $this->db->where('au.audit_id', $audit_id);
        if ($this->db->field_exists('dtform_urut', 'detailform')) {
            $this->db->order_by('dt.dtform_urut', 'asc');
        }
        $this->db->order_by('dj.dtjwb_id', 'asc');
        return $this->db->get()->result_array();
    }

    function get_datatables($length, $start, $search, $ordering, $periode_id = NULL) {
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $this->_query_ptk(NULL, FALSE, $periode_id);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering, $periode_id = NULL) {
        $this->_get_datatables_query($search, $ordering);
        $this->_query_ptk(NULL, FALSE, $periode_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($periode_id = NULL) {
        $this->_query_ptk(NULL, FALSE, $periode_id);
        return $this->db->count_all_results();
    }

    /**
     * Ringkasan temuan dalam satu kueri: total, jumlah per kategori, dan
     * jumlah yang belum punya rencana koreksi (dipakai kartu statistik
     * halaman PTK - angkanya sama dengan isi tabel).
     *
     * @param int|null $periode_id batasi ke satu periode audit (filter PTK)
     */
    public function ringkasan($periode_id = NULL) {
        $koreksi = $this->_koreksi_sql();

        $this->db->select('COUNT(*) AS total', FALSE);
        $this->db->select("SUM(CASE WHEN dj.dtjwb_temuan = 'OB' THEN 1 ELSE 0 END) AS observasi", FALSE);
        $this->db->select("SUM(CASE WHEN dj.dtjwb_temuan = 'TS MINOR' THEN 1 ELSE 0 END) AS minor", FALSE);
        $this->db->select("SUM(CASE WHEN dj.dtjwb_temuan = 'TS MAYOR' THEN 1 ELSE 0 END) AS mayor", FALSE);
        $this->db->select("SUM(CASE WHEN ($koreksi IS NULL OR TRIM($koreksi) = '') THEN 1 ELSE 0 END) AS tanpa_koreksi", FALSE);
        $this->db->from('auditjawabdetail dj');
        $this->db->join('auditjawab jb', 'jb.jwb_id = dj.jwb_id', 'inner');
        $this->db->join('audit ma', 'ma.audit_id = jb.audit_id', 'inner');
        $this->db->where("dj.dtjwb_temuan IN ('OB','TS MINOR','TS MAYOR')");

        /* Saringan periode (sama dengan _query_ptk): kolom melekat pada audit. */
        if (!empty($periode_id) && $this->db->field_exists('periode_id', 'audit')) {
            $this->db->where('ma.periode_id', (int) $periode_id);
        }

        /* Saringan pemilik (sama dengan _query_ptk). */
        $this->_saringPemilik('ma');

        $row = $this->db->get()->row_array();

        return array(
            'total'         => isset($row['total']) ? (int) $row['total'] : 0,
            'observasi'     => isset($row['observasi']) ? (int) $row['observasi'] : 0,
            'minor'         => isset($row['minor']) ? (int) $row['minor'] : 0,
            'mayor'         => isset($row['mayor']) ? (int) $row['mayor'] : 0,
            'tanpa_koreksi' => isset($row['tanpa_koreksi']) ? (int) $row['tanpa_koreksi'] : 0,
        );
    }

    /** Jumlah butir satu kategori temuan (kartu statistik). */
    private function _totalTemuan($nilai, $periode_id = NULL) {
        $this->db->from('auditjawabdetail dj');
        $this->db->join('auditjawab jb', 'jb.jwb_id = dj.jwb_id', 'inner');
        $this->db->join('audit ma', 'ma.audit_id = jb.audit_id', 'inner');
        $this->db->where('dj.dtjwb_temuan', $nilai);

        if (!empty($periode_id) && $this->db->field_exists('periode_id', 'audit')) {
            $this->db->where('ma.periode_id', (int) $periode_id);
        }

        $this->_saringPemilik('ma');

        return $this->db->count_all_results();
    }

    public function totalObservasi($periode_id = NULL) {
        return $this->_totalTemuan('OB', $periode_id);
    }

    public function totalMinor($periode_id = NULL) {
        return $this->_totalTemuan('TS MINOR', $periode_id);
    }

    public function totalMayor($periode_id = NULL) {
        return $this->_totalTemuan('TS MAYOR', $periode_id);
    }

    /**
     * Simpan rencana koreksi satu butir pada `auditjawabdetail.dtjwb_koreksi`.
     *
     * Kolom lama `auditjawab.jwb_koreksi` sudah dihapus, jadi bila kolom
     * `dtjwb_koreksi` belum tersedia penyimpanan dibatalkan (FALSE).
     * $data: audit_id, jwb_id, dtjwb_id, jwb_koreksi.
     */
    public function koreksi($data) {
        /* Rencana koreksi hanya diisi auditee, dan hanya setelah audit
           berstatus SELESAI. Penjagaan di model ini melindungi semua
           pemanggil (halaman PTK maupun halaman delik). */
        $audit = $this->db->select('audit_status')
            ->where('audit_id', $data['audit_id'])
            ->get('audit')->row_array();
        if (empty($audit) || strtoupper(trim((string) $audit['audit_status'])) !== 'SELESAI') {
            return FALSE;
        }

        $this->db->trans_start();

        if (!$this->koreksiSiap()) {
            $this->db->trans_complete();
            return FALSE;
        }

        $this->db->where('dtjwb_id', $data['dtjwb_id']);
        $this->db->update('auditjawabdetail', array('dtjwb_koreksi' => $data['jwb_koreksi']));

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Satu butir tilik (untuk modal rencana koreksi / penjagaan simpan). */
    public function getButir($audit_id, $dtjwb_id) {
        /* "S" ikut disertakan supaya modal tetap bisa dibuka untuk semua baris
           dan penjagaan simpan memakai baris yang sama dengan yang tampil. */
        $this->_query_ptk(NULL, TRUE);
        $this->db->where('au.audit_id', $audit_id);
        $this->db->where('dj.dtjwb_id', $dtjwb_id);
        return $this->db->get()->row_array();
    }

    public function is_exist($data) {

        $this->db->where("audit_id",$data['audit_id']);
        $this->db->where("dtform_id",$data['dtform_id']);
        $query = $this->db->get('auditjawab');
        if ($query->num_rows() > 0) {
            return true; // data ada
        } else {
            return false; // data tidak ada
        }
    }

    public function hapus($id) {
        $this->db->where('jwb_id',$id);
        $this->db->from('auditjawab');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

}
