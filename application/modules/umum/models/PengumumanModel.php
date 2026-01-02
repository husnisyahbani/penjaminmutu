<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PengumumanModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('pengumuman_judul','pengumuman_deskripsi','pengumuman_file','pengumuman_isi','pengumuman_create');
    var $column_order = array(null,'pengumuman_judul','pengumuman_deskripsi',null,'pengumuman_isi','pengumuman_create',null);
    var $order = array('pengumuman_create' => 'desc');

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
        
        $this->db->from('pengumuman');
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->from('pengumuman');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from('pengumuman');
        return $this->db->count_all_results();
    }

    public function add($data) {
        $this->db->insert('pengumuman',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('pengumuman_id',$id);
        $this->db->from('pengumuman');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    function getPengumumanById($id) {
        $this->db->where('pengumuman_id',$id);
        $this->db->from('pengumuman');
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllPengumuman() {
        $this->db->from('pengumuman');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("pengumuman_id",$data['pengumuman_id']);
        $this->db->update('pengumuman',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
