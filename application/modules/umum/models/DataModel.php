<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class DataModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function add($data) {
        $this->db->insert('mutu_data',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('data_id',$id);
        $this->db->from('mutu_data');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    function getAllPenetapan() {
        $this->db->from('mutu_data');
        $this->db->where('data_kategori','PENETAPAN');
        $this->db->where('show','1');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllPelaksanaan() {
        $this->db->from('mutu_data');
        $this->db->where('data_kategori','PELAKSANAAN');
        $this->db->where('show','1');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllEvaluasi() {
        $this->db->from('mutu_data');
        $this->db->where('data_kategori','EVALUASI');
        $this->db->where('show','1');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllPengendalian() {
        $this->db->from('mutu_data');
        $this->db->where('data_kategori','PENGENDALIAN');
        $this->db->where('show','1');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllPeningkatan() {
        $this->db->from('mutu_data');
        $this->db->where('data_kategori','PENINGKATAN');
        $this->db->where('show', '1');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getData($id) {
        $this->db->where('data_id',$id);
        $this->db->from('mutu_data');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("data_id",$data['data_id']);
        $this->db->update('mutu_data',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
