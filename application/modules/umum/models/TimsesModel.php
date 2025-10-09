<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class TimsesModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $table = 'timses';
    var $column_search = array('tms_nama','tms_alamat','tms_nik' ,'provinces.name','regencies.name','districts.name','villages.name','tms_tps');
    var $column_order = array(null,null,'tms_nama','tms_alamat','tms_nik' ,'provinces.name','regencies.name','districts.name','villages.name','tms_tps',null);
    var $order = array('timses.tms_id' => 'desc');

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

        if (isset($ordering)) {
            $this->db->order_by($this->column_order[$ordering[0]['column']], $ordering[0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($length, $start, $search, $ordering) {
        $this->db->select("*");
        $this->db->select("regencies.name AS kota");
        $this->db->select("provinces.name AS provinsi");
        $this->db->select("districts.name AS kecamatan");
        $this->db->select("villages.name AS kelurahan");
        
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        
        $this->db->from($this->table);
        $this->db->join('regencies', 'regencies.id = timses.tms_regencies_id', 'left');
        $this->db->join('provinces', 'provinces.id = timses.tms_provinces_id', 'left');
        $this->db->join('districts', 'districts.id = timses.tms_districts_id', 'left');
        $this->db->join('villages', 'villages.id = timses.tms_villages_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->from($this->table);
        $this->db->join('regencies', 'regencies.id = timses.tms_regencies_id', 'left');
        $this->db->join('provinces', 'provinces.id = timses.tms_provinces_id', 'left');
        $this->db->join('districts', 'districts.id = timses.tms_districts_id', 'left');
        $this->db->join('villages', 'villages.id = timses.tms_villages_id', 'left');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function hapus($id) {
        $this->db->where("tms_id",$id);
        $this->db->from($this->table);
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }
    
    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("tms_id",$data['tms_id']);
        $this->db->update($this->table,$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function add($data) {
        $this->db->insert($this->table,$data);
        $akun = array();
        $akun['username'] = $data['tms_nik'];
        $akun['password'] = md5($data['tms_nik']);
        $akun['role'] = 'RELAWAN';
        $this->db->insert('users',$akun);
        return($this->db->affected_rows() != 1) ? false : true;
    }
    
    function is_exist($data) {
        $this->db->from($this->table);
        $this->db->where("tms_nik", $data['tms_nik']);
        $this->db->where("role", 'RELAWAN');
        $query = $this->db->get();
        return $query->num_rows()>0?TRUE:FALSE;
    }
    
    
    function getTimses() {
        $this->db->from($this->table);
        $this->db->limit(3);
        $this->db->order_by('tms_id', 'DESC');
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
    
    function getTimsesById($id) {
        $this->db->where("tms_id",$id);
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getAllTimses() {
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getTotalTimses() {
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->num_rows();
    }

}
