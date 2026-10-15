<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Sumber data halaman PTK dan halaman delik auditee.
 *
 * Seluruh kueri kini berada pada model bersama
 * application/models/PtkdataModel.php dengan pemilik baris = 'auditee',
 * sehingga halaman PTK auditee, PTK auditor, dan PTK PPM menampilkan data
 * dengan aturan yang sama persis; yang berbeda hanya baris yang boleh
 * dilihat (auditee: auditnya sendiri, auditor: tilik yang ia buat, PPM:
 * semuanya).
 *
 * Model ini tinggal meneruskan pemanggilan ke PtkdataModel supaya controller
 * dan view lama tidak perlu diubah.
 *
 * Satu baris tabel = satu butir tilik hasil penilaian auditor, yaitu baris
 * `mutu_auditjawabdetail` (dtjwb_pertanyaan / dtjwb_hasil / dtjwb_temuan /
 * dtjwb_catatan) yang menempel pada jawaban satu pertanyaan
 * (`mutu_auditjawab` -> audit_id + dtform_id).
 *
 * Rencana koreksi disimpan per butir pada `auditjawabdetail.dtjwb_koreksi`
 * (lihat database/koreksi_butir.sql atau menu Migrasi).
 */
class PtkModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Model bersama beserta kuerinya. Baris dibatasi hanya pada audit milik
     * auditee yang sedang masuk.
     */
    private function _data() {
        $this->load->model('PtkdataModel', 'ptkdata');
        $this->ptkdata->aturPemilik('auditee');
        return $this->ptkdata;
    }

    /** Sudah ada kolom koreksi per butir? */
    public function koreksiSiap() {
        return $this->_data()->koreksiSiap();
    }

    /**
     * Daftar butir satu audit untuk halaman delik (tanpa paging DataTables).
     */
    public function daftar($audit_id, $dtform_id = NULL, $termasuk_s = FALSE) {
        return $this->_data()->daftar($audit_id, $dtform_id, $termasuk_s);
    }

    function get_datatables($length, $start, $search, $ordering, $periode_id = NULL) {
        return $this->_data()->get_datatables($length, $start, $search, $ordering, $periode_id);
    }

    function count_filtered($search, $ordering, $periode_id = NULL) {
        return $this->_data()->count_filtered($search, $ordering, $periode_id);
    }

    public function count_all($periode_id = NULL) {
        return $this->_data()->count_all($periode_id);
    }

    /**
     * Ringkasan temuan unit auditee ini: total, jumlah per kategori, dan
     * jumlah yang belum punya rencana koreksi.
     */
    public function ringkasan($periode_id = NULL) {
        return $this->_data()->ringkasan($periode_id);
    }

    public function totalObservasi($periode_id = NULL) {
        return $this->_data()->totalObservasi($periode_id);
    }

    public function totalMinor($periode_id = NULL) {
        return $this->_data()->totalMinor($periode_id);
    }

    public function totalMayor($periode_id = NULL) {
        return $this->_data()->totalMayor($periode_id);
    }

    /**
     * Simpan rencana koreksi satu butir pada `auditjawabdetail.dtjwb_koreksi`.
     * $data: audit_id, jwb_id, dtjwb_id, jwb_koreksi.
     */
    public function koreksi($data) {
        return $this->_data()->koreksi($data);
    }

    /** Satu butir tilik (untuk modal rencana koreksi / penjagaan simpan). */
    public function getButir($audit_id, $dtjwb_id) {
        return $this->_data()->getButir($audit_id, $dtjwb_id);
    }

    public function is_exist($data) {
        return $this->_data()->is_exist($data);
    }

    public function hapus($id) {
        return $this->_data()->hapus($id);
    }

    public function statusSiap() {
        return $this->_data()->statusSiap();
    }

    public function updateStatus($dtjwb_id, $status) {
        return $this->_data()->updateStatus($dtjwb_id, $status);
    }

}

