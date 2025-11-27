<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class DtjwbModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('dtjwb_referensi','dtjwb_pertanyaan','dtjwb_temuan','dtjwb_hasil','dtjwb_catatan');
    var $column_order = array(null,'dtjwb_referensi','dtjwb_pertanyaan','dtjwb_temuan','dtjwb_hasil','dtjwb_catatan',null);
    var $order = array('dtjwb_id ' => 'asc');

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
        $this->db->where('jwb_id',$id);
        $this->db->from('mutu_auditjawabdetail');
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->where('jwb_id',$id);
        $this->db->from('mutu_auditjawabdetail');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id) {
        $this->db->where('jwb_id',$id);
        $this->db->from('mutu_auditjawabdetail');
        return $this->db->count_all_results();
    }

    function getJwbid($audit_id,$dtform_id){ 
        $this->db->from('mutu_auditjawab');
        $this->db->where('audit_id',$audit_id);
        $this->db->where('dtform_id',$dtform_id);
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['jwb_id'];
    }

    function getjawabById($dtjwb_id){ 
        $this->db->from('mutu_auditjawabdetail');
        $this->db->where('dtjwb_id',$dtjwb_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    

    public function add($data) {
        $this->db->insert('mutu_auditjawabdetail',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('dtjwb_id',$id);
        $this->db->from('mutu_auditjawabdetail');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("dtjwb_id",$data['dtjwb_id']);
        $this->db->update('mutu_auditjawabdetail',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
