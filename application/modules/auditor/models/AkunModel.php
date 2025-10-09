<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AkunModel extends CI_Model {


    function __construct() {
        parent::__construct();
    }

    var $table = 'users';

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
    
    
    function getAkunById($id) {
        $this->db->from($this->table);
        $this->db->where("users_id", $id);
        $query = $this->db->get();
        return $query->row();
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
