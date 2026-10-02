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

    var $column_search = array('form_nama','form_kode','form_deskripsi','form_create');
    // Urutan kolom tabel: No | Nama | Kode | Deskripsi | Periode | Tanggal | Aksi
    var $column_order = array(null,'form_nama','form_kode','form_deskripsi',null,'form_create',null);
    var $order = array('form_create' => 'desc');

    private function _get_datatables_query($search, $ordering, $periode_id = NULL) {
        if (!empty($search['value'])) {
            $this->db->group_start();
            $this->db->like('f.form_nama', $search['value']);
            $this->db->or_like('f.form_kode', $search['value']);
            $this->db->or_like('f.form_deskripsi', $search['value']);
            $this->db->or_like('f.form_create', $search['value']);
            // Periode formulir ikut tercari bila kolomnya sudah ada.
            if ($this->periodeSiap() && $this->db->table_exists($this->tabel_periode)) {
                $this->db->or_like('p.periode_tahun', $search['value']);
            }
            $this->db->group_end();
        }

        if (isset($ordering[0]['column']) && !empty($this->column_order[$ordering[0]['column']])) {
            $this->db->order_by($this->column_order[$ordering[0]['column']], $ordering[0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

        /* Filter periode. Formulir lama yang belum punya periode tetap ikut
           tampil (ditandai '-' pada kolom Periode) supaya tidak "hilang"
           begitu fitur periode dipasang; pilih "Semua Periode" untuk
           menampilkan seluruh formulir. */
        if (!empty($periode_id) && $this->periodeSiap()) {
            $this->db->group_start();
            $this->db->where('f.periode_id', $periode_id);
            $this->db->or_where('f.periode_id', NULL);
            $this->db->group_end();
        }
    }

    /** Kolom yang diambil + relasi periode (bila kolomnya sudah ada). */
    private function _select_formulir() {
        $this->db->select('f.*');
        // from() harus lebih dahulu: alias 'f' perlu dikenali agar
        // kondisi join tidak ikut ditambahi prefix tabel.
        $this->db->from('formulir f');
        if ($this->periodeSiap() && $this->db->table_exists($this->tabel_periode)) {
            $this->db->select('p.periode_tahun');
            $this->db->join($this->tabel_periode . ' p', 'p.periode_id = f.periode_id', 'left');
        }
    }

    function get_datatables($length, $start, $search, $ordering, $periode_id = NULL) {
        $this->_get_datatables_query($search, $ordering, $periode_id);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('f.users_id',$users_id);
        }
        $this->_select_formulir();
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering, $periode_id = NULL) {
        $this->_get_datatables_query($search, $ordering, $periode_id);

        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('f.users_id',$users_id);
        }
        $this->_select_formulir();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($periode_id = NULL) {
        if (!empty($periode_id) && $this->periodeSiap()) {
            // Sama seperti daftar: formulir tanpa periode ikut dihitung.
            $this->db->group_start();
            $this->db->where('f.periode_id', $periode_id);
            $this->db->or_where('f.periode_id', NULL);
            $this->db->group_end();
        }

        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('f.users_id',$users_id);
        }
        $this->db->from('formulir f');
        return $this->db->count_all_results();
    }

    public function add($data) {
        $data['users_id'] = $this->session->userdata('users_id');

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

    /** Satu pertanyaan (detailform) - dipakai halaman daftar tilik. */
    function getSoalFormulir($dtform_id) {
        $this->db->from('detailform');
        $this->db->where('dtform_id', $dtform_id);
        return $this->db->get()->row_array();
    }

    function getFormulir($id) {
        $this->db->where('form_id',$id);
        $this->db->from('formulir');
        $query = $this->db->get();
        return $query->row_array();
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
        $query = $this->db->get();
        return $query->result_array();
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("form_id",$data['form_id']);
        $this->db->update('formulir',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
