<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SkModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('sk_judul','sk_create');
    var $column_order = array(null,'sk_judul',null,'sk_create',null);
    var $order = array('sk_create' => 'desc');

    private function _get_datatables_query($search, $ordering) {
        $i = 0;

        foreach ($this->column_search as $item) { // looping awal
            if ($search['value']) { // jika sktable mengirimkan pencarian dengan metode POST
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
        $this->db->from('sk');
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->from('sk');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from('sk');
        return $this->db->count_all_results();
    }

    public function add($sk) {
        $this->db->insert('sk',$sk);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('sk_id',$id);
        $this->db->from('sk');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    function getData($id) {
        $this->db->where('sk_id',$id);
        $this->db->from('sk');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit($sk) {
        $this->db->trans_start();
        $this->db->where("sk_id",$sk['sk_id']);
        $this->db->update('sk',$sk);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
