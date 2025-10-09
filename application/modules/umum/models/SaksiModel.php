<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SaksiModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $table = 'saksi';
    var $column_search = array('saksi_nama','saksi_alamat','saksi_nik' ,'regencies.name','districts.name','villages.name','saksi_tps');
    var $column_order = array(null,null,'saksi_nama','saksi_alamat','saksi_nik' ,'regencies.name','districts.name','villages.name','saksi_tps','users.valid',null);
    var $order = array('users.valid' => 'asc');

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
        $this->db->where("users.role","SAKSI");
        $this->db->from($this->table);
        $this->db->join('regencies', 'regencies.id = saksi.saksi_regencies_id', 'left');
        $this->db->join('provinces', 'provinces.id = saksi.saksi_provinces_id', 'left');
        $this->db->join('districts', 'districts.id = saksi.saksi_districts_id', 'left');
        $this->db->join('villages', 'villages.id = saksi.saksi_villages_id', 'left');
        $this->db->join('users', 'users.username = saksi.saksi_nik', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->where("users.role","SAKSI");
        $this->db->from($this->table);
        $this->db->join('regencies', 'regencies.id = saksi.saksi_regencies_id', 'left');
        $this->db->join('provinces', 'provinces.id = saksi.saksi_provinces_id', 'left');
        $this->db->join('districts', 'districts.id = saksi.saksi_districts_id', 'left');
        $this->db->join('villages', 'villages.id = saksi.saksi_villages_id', 'left');
        $this->db->join('users', 'users.username = saksi.saksi_nik', 'left');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->where("users.role","SAKSI");
        $this->db->from($this->table);
        $this->db->join('users', 'users.username = saksi.saksi_nik', 'left');
        return $this->db->count_all_results();
    }
    
    public function hapus($id) {
        $this->db->where("saksi_id",$id);
        $this->db->from($this->table);
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }
    
    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("saksi_id",$data['saksi_id']);
        $this->db->update($this->table,$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function add($data) {
        $this->db->insert($this->table,$data);
        $akun = array();
        $akun['username'] = $data['saksi_nik'];
        $akun['password'] = md5($data['saksi_nik']);
        $akun['role'] = 'SAKSI';
        $this->db->insert('users',$akun);
        return($this->db->affected_rows() != 1) ? false : true;
    }
    
    function is_exist($data) {
        $this->db->from($this->table);
        $this->db->where("saksi_nik", $data['saksi_nik']);
        $this->db->where("role", 'SAKSI');
        $query = $this->db->get();
        return $query->num_rows()>0?TRUE:FALSE;
    }

    function is_notfree($data) {
        $this->db->where("users.valid","1");
        $this->db->where("users.role","SAKSI");
        $this->db->where("saksi.saksi_villages_id", $data['saksi_villages_id']);
        $this->db->where("saksi.saksi_tps", $data['saksi_tps']);
        $this->db->from($this->table);
        $this->db->join('users', 'users.username = saksi.saksi_nik', 'left');
        $query = $this->db->get();
        return $query->num_rows()>0?TRUE:FALSE;
    }
    
    
    function getSaksi() {
        $this->db->from($this->table);
        $this->db->limit(3);
        $this->db->order_by('saksi_id', 'DESC');
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
    
    function getSaksiById($id) {
        $this->db->where("saksi_id",$id);
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
    
    function getTotalSaksi() {
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function getGambar($data) {
        $this->db->select('saksi_foto');
        $this->db->where('saksi_id',$data['saksi_id']);
        $this->db->from($this->table);
        $query = $this->db->get();
        header('Content-Type: application/json');
        return json_encode($query->result());
    }
    
    function getAllSaksi(){
        $this->db->select("*");
        $this->db->select("regencies.name AS kota");
        $this->db->select("provinces.name AS provinsi");
        $this->db->select("districts.name AS kecamatan");
        $this->db->select("villages.name AS kelurahan");
        $this->db->where("users.role","SAKSI");      
        $this->db->from($this->table);
        $this->db->join('regencies', 'regencies.id = saksi.saksi_regencies_id', 'left');
        $this->db->join('provinces', 'provinces.id = saksi.saksi_provinces_id', 'left');
        $this->db->join('districts', 'districts.id = saksi.saksi_districts_id', 'left');
        $this->db->join('villages', 'villages.id = saksi.saksi_villages_id', 'left');
        $this->db->join('users', 'users.username = saksi.saksi_nik', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

}
