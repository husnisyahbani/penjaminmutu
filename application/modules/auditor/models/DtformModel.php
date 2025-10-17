<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class DtformModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('dtform_tujuan','dtform_pertanyaan','dtform_lingkup','dtform_create');
    var $column_order = array(null,'dtform_tujuan','dtform_pertanyaan','dtform_lingkup','dtform_create',null);
    var $order = array('dtform_create' => 'desc');

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

    function get_datatables($length, $start, $search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        
        $this->db->from('detailform');
        $this->db->where("form_id",$id);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->from('detailform');
        $this->db->where("form_id",$id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id) {
        $this->db->from('detailform');
        $this->db->where("form_id",$id);
        return $this->db->count_all_results();
    }

    public function add($data) {
        $this->db->insert('detailform',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('dtform_id',$id);
        $this->db->from('detailform');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    function getdetailform($id) {
        $this->db->where('dtform_id',$id);
        $this->db->from('detailform');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("dtform_id",$data['dtform_id']);
        $this->db->update('detailform',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
