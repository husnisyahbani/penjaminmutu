<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class FormulirModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('form_nama','form_kode','form_deskripsi','form_create');
    var $column_order = array(null,'form_nama','form_kode','form_deskripsi','form_create',null);
    var $order = array('form_create' => 'desc');

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
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('users_id',$users_id);
        }
        $this->db->from('formulir');
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('users_id',$users_id);
        }
        $this->db->from('formulir');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('users_id',$users_id);
        }
        $this->db->from('formulir');
        return $this->db->count_all_results();
    }

    public function add($data) {
        $data['users_id'] = $this->session->userdata('users_id');
        $this->db->insert('formulir',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('form_id',$id);
        $this->db->from('formulir');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    function getFormulir($id) {
        $this->db->where('form_id',$id);
        $this->db->from('formulir');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllFormulir() {
        $this->db->from('formulir');
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('users_id',$users_id);
        }
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
