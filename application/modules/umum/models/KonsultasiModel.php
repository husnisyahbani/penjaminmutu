<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class KonsultasiModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $table = 'konsultasi';
    var $column_search = array('konsul_judul','konsul_email','konsul_isi','konsul_createtime');
    var $column_order = array(null,'konsul_judul','konsul_email','konsul_isi','konsul_file','konsul_balas','konsul_createtime',null);
    var $order = array('konsultasi.konsul_id' => 'desc');

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
    
    
    public function add($data) {
        $this->db->insert($this->table,$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }
    
    

}
