<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class DtformModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /* 'lingkup' bukan nama kolom lagi: pencariannya lewat EXISTS ke tabel lingkup. */
    var $column_search = array('dtform_tujuan','dtform_pertanyaan','lingkup','dtform_create');
    var $column_order = array(null,'dtform_tujuan','dtform_pertanyaan','dtform_create','dtform_create',null);
    var $order = array('dtform_create' => 'desc');

    private function _get_datatables_query($search, $ordering) {
        /* Pencarian menyertakan butir lingkup (tabel lingkup) lewat EXISTS. */
        if (!empty($search['value'])) {
            $kunci = $this->db->escape_like_str($search['value']);

            $this->db->group_start();
            $this->db->like('df.dtform_pertanyaan', $search['value']);
            $this->db->or_like('df.dtform_tujuan', $search['value']);
            if ($this->db->table_exists('lingkup')) {
                $tabel = $this->db->dbprefix('lingkup');
                $this->db->or_where(
                    "EXISTS (SELECT 1 FROM `" . $tabel . "` lg WHERE lg.dtform_id = df.dtform_id AND lg.lingkup_isi LIKE '%" . $kunci . "%')",
                    NULL, FALSE
                );
            }
            $this->db->group_end();
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
        
        $this->db->from('detailform AS df');
        $this->db->where('df.form_id', $id);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->from('detailform AS df');
        $this->db->where('df.form_id', $id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id) {
        $this->db->from('detailform AS df');
        $this->db->where('df.form_id', $id);
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

    function getAllDtformByFormId($form_id){
        $this->db->where('form_id',$form_id);
        $this->db->from('detailform');
        $query = $this->db->get();
        return $query->result_array();
    }

    /** Daftar pertanyaan satu formulir (dipakai di luar tabel). */
    function getByFormId($form_id) {
        $this->db->where('form_id', $form_id);
        $this->db->order_by('dtform_id', 'ASC');
        return $this->db->get('detailform')->result_array();
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("dtform_id",$data['dtform_id']);
        $this->db->update('detailform',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
