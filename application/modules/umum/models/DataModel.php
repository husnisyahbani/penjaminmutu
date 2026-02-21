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
    return $this->db
        ->from('mutu_data')
        ->where('data_kategori', 'PENETAPAN')
        ->where('isshow', '1')
        ->order_by('data_create', 'DESC')
        ->get()
        ->result_array();   // ← INI KUNCI
}

    function getAllPelaksanaan() {
    return $this->db
        ->from('mutu_data')
        ->where('data_kategori', 'PELAKSANAAN')
        ->where('isshow', '1')
        ->order_by('data_create', 'DESC')
        ->get()
        ->result_array();   // ← INI KUNCI
}

    function getAllEvaluasi() {
        return $this->db
        ->from('mutu_data')
        ->where('data_kategori', 'EVALUASI')
        ->where('isshow', '1')
        ->order_by('data_create', 'DESC')
        ->get()
        ->result_array();   // ← INI KUNCI
    }

    function getAllPengendalian() {
        return $this->db
        ->from('mutu_data')
        ->where('data_kategori', 'PENGENDALIAN')
        ->where('isshow', '1')
        ->order_by('data_create', 'DESC')
        ->get()
        ->result_array();   // ← INI KUNCI
    }

    function getAllPeningkatan() {
        return $this->db
        ->from('mutu_data')
        ->where('data_kategori', 'PENINGKATAN')
        ->where('isshow', '1')
        ->order_by('data_create', 'DESC')
        ->get()
        ->result_array();   // ← INI KUNCI
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
