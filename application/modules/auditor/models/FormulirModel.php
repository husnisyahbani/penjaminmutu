<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FormulirModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    

    public function add($data) {
        $this->db->insert('formulir',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('form_id',$id);
        $this->db->from('formulir');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    function getAllFormulir() {
        $this->db->from('formulir');
        $query = $this->db->get();
        return $query->result_array();
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
