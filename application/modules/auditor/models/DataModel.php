<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class DataModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('data_keterangan','data_uraian','data_file','data_kategori','data_create');
    var $column_order = array(null,'data_keterangan','data_uraian',null,'data_kategori','data_create',null);
    var $order = array('data_create' => 'desc');

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
        $this->db->select('data_id');
        $this->db->select('data_uraian');
        $this->db->select('data_keterangan');
        $this->db->select('data_file');
        $this->db->select('data_kategori');
        $this->db->select('data_create');
        $this->db->from('data');
        if(isset($id))
        $this->db->where('data_kategori',$id);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->select('data_id');
        $this->db->select('data_uraian');
        $this->db->select('data_keterangan');
        $this->db->select('data_file');
        $this->db->select('data_kategori');
        $this->db->select('data_create');
        $this->db->from('data');
        if(isset($id))
        $this->db->where('data_kategori',$id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id) {
        $this->db->select('data_id');
        $this->db->select('data_uraian');
        $this->db->select('data_keterangan');
        $this->db->select('data_file');
        $this->db->select('data_kategori');
        $this->db->select('data_create');
        $this->db->from('data');
        if(isset($id))
        $this->db->where('data_kategori',$id);
        return $this->db->count_all_results();
    }


    function get_datatables_info($length, $start, $search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $this->db->select('data_id');
        $this->db->select('data_uraian');
        $this->db->select('data_keterangan');
        $this->db->select('data_file');
        $this->db->select('data_create');
        $this->db->where('data_kategori','INFO');
        $this->db->from('data');
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_info($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->select('data_id');
        $this->db->select('data_uraian');
        $this->db->select('data_keterangan');
        $this->db->select('data_file');
        $this->db->select('data_create');
        $this->db->where('data_kategori','INFO');
        $this->db->from('data');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_info() {
        $this->db->select('data_id');
        $this->db->select('data_uraian');
        $this->db->select('data_keterangan');
        $this->db->select('data_file');
        $this->db->select('data_create');
        $this->db->where('data_kategori','INFO');
        $this->db->from('data');
        return $this->db->count_all_results();
    }


    public function add($data) {
        $this->db->insert('data',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('data_id',$id);
        $this->db->from('data');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    function getData($id) {
        $this->db->where('data_id',$id);
        $this->db->from('data');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("data_id",$data['data_id']);
        $this->db->update('data',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
