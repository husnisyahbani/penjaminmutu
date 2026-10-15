<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AkunModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $table = 'users';
    var $column_search = array('nama','unitkerja','username','role');
    var $column_order = array(null,'nama','unitkerja','username','password','role',null);
    var $order = array('users.users_id' => 'asc');

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

        if (isset($ordering) && isset($this->column_order[$ordering[0]['column']])) {
            /* Penjagaan: abaikan permintaan pengurutan pada kolom yang tidak
               dikenal (mis. kolom Status/Aksi yang baru ditambahkan). */
            if ($this->column_order[$ordering[0]['column']] !== NULL) {
                $this->db->order_by($this->column_order[$ordering[0]['column']], $ordering[0]['dir']);
            }
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($length, $start, $search, $ordering) {
        $this->db->select("*");
        
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    /* ==================================================================
       Status akun: Aktif (valid = 1) / Tidak Aktif (valid = 0)
       ================================================================== */

    /** Kolom penanda status (users.valid) tersedia pada database ini? */
    public function statusSiap() {
        return $this->db->field_exists('valid', $this->table);
    }

    /** Abaikan akun yang sudah dinonaktifkan (bila kolomnya tersedia). */
    private function _hanyaAktif() {
        if ($this->statusSiap()) {
            $this->db->where('(valid IS NULL OR valid = 1)', NULL, FALSE);
        }
    }

    /** Aktifkan (TRUE) atau nonaktifkan (FALSE) satu akun. */
    public function ubahStatus($id, $aktif) {
        if (!$this->statusSiap() || empty($id)) {
            return FALSE;
        }

        $this->db->where('users_id', (int) $id);
        $this->db->set('valid', $aktif ? 1 : 0);
        return $this->db->update($this->table);
    }

    /** Jumlah akun yang masih aktif, seluruhnya atau per peran. */
    public function hitungAktif($role = NULL) {
        $this->db->from($this->table);
        if (!empty($role)) {
            $this->db->where('role', $role);
        }
        $this->_hanyaAktif();
        return (int) $this->db->count_all_results();
    }
    
    public function hapus($id) {
        $this->db->where("users_id",$id);
        $this->db->from($this->table);
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }
    
    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("users_id",$data['users_id']);
        $this->db->update($this->table,$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function add($data) {
        $this->db->insert($this->table,$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }
    
    function is_exist($data) {
        $this->db->from($this->table);
        $this->db->where("username", $data['username']);
        $query = $this->db->get();
        return $query->num_rows()>0?TRUE:FALSE;
    }
    
    
    function getAkun() {
        $this->db->from($this->table);
        $this->db->limit(3);
        $query = $this->db->get();
        return $query->result_array();
    }

    
    function getProvinces($id) {
        $this->db->select("nama");
        $this->db->where("id",$id);
        $this->db->from("provinces");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getAkunById($id) {
        $this->db->where("users_id",$id);
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllAuditor() {
        $this->db->from($this->table);
        $this->db->where("role","AUDITOR");
        /* Akun yang dinonaktifkan tidak lagi ditawarkan sebagai auditor. */
        $this->_hanyaAktif();
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllAuditee() {
        $this->db->from($this->table);
        $this->db->where("role","AUDITEE");
        /* Akun yang dinonaktifkan tidak lagi ditawarkan sebagai auditee. */
        $this->_hanyaAktif();
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getAllAkun() {
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getTotalAkun() {
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function passlama($passlama) { 
        $username = $this->session->userdata('username');
        $this->db->where('username', $username);
        $this->db->where('password', $passlama);
        $this->db->from('users');
        $query = $this->db->get();
        return $query->num_rows()>0?TRUE:FALSE;
        
    }
    

}
