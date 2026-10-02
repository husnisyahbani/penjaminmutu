<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FormulirModel extends CI_Model {

    /* Relasi ke periode: mutu_formulir.periode_id -> mutu_periode.periode_id */
    var $kol_periode   = 'periode_id';
    var $tabel_periode = 'mutu_periode';

    function __construct() {
        parent::__construct();
    }

    /** Kolom relasi periode sudah ada? (database/formulir_periode.sql) */
    function periodeSiap() {
        return $this->db->field_exists($this->kol_periode, 'formulir');
    }

    /** Periode yang sedang aktif (NULL bila tidak ada / belum dipasang). */
    private function _aktifId() {
        if (!$this->db->table_exists($this->tabel_periode)) {
            return NULL;
        }
        $row = $this->db->where('periode_aktif', 1)
                        ->order_by('periode_id', 'DESC')
                        ->get($this->tabel_periode)->row_array();
        return isset($row['periode_id']) ? (int) $row['periode_id'] : NULL;
    }

    

    public function add($data) {
        // Formulir baru mengikuti periode aktif bila pengguna tidak memilih.
        // array_key_exists() dipakai agar pilihan "Tanpa Periode"
        // (periode_id = NULL) tidak ikut diisi periode aktif.
        if ($this->periodeSiap() && !array_key_exists('periode_id', $data)) {
            $data['periode_id'] = $this->_aktifId();
        }

        $this->db->insert('formulir',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('form_id',$id);
        $this->db->from('formulir');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    /**
     * Seluruh formulir (untuk pilihan pada form audit).
     * Bila $periode_id diisi: formulir periode tersebut + formulir lama yang
     * belum berperiode, supaya data lama tetap bisa dipakai.
     */
    function getAllFormulir($periode_id = NULL) {
        if (!empty($periode_id) && $this->periodeSiap()) {
            $this->db->group_start();
            $this->db->where('periode_id', $periode_id);
            $this->db->or_where('periode_id IS NULL', NULL, FALSE);
            $this->db->group_end();
        }

        $this->db->from('formulir');
        $this->db->join('detailform', 'detailform.form_id = formulir.form_id', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getSoalFormulir($dtform_id) {
        $this->db->from('detailform');
        $this->db->where('dtform_id',$dtform_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getFormulir($id) {
        $this->db->where('form_id',$id);
        $this->db->from('formulir');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("form_id",$data['form_id']);
        $this->db->update('formulir',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
