<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AuditjawabModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('dtform_pertanyaan','dtform_lingkup','jwb_jawaban','jwb_hasil','jwb_temuan','jwb_catatan');
    var $column_order = array(null,'dtform_pertanyaan','dtform_lingkup','jwb_jawaban','jwb_hasil','jwb_temuan','jwb_catatan');
    var $order = array('audit_id' => 'asc');

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
        $this->db->select("audit_id");
        $this->db->select("audit_status");
        $this->db->select("dt.dtform_id as dtform_id");
        $this->db->select("dt.dtform_pertanyaan as dtform_pertanyaan");
        $this->db->select("dt.dtform_lingkup as dtform_lingkup");
        $this->db->select("(SELECT jwb_catatan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_catatan");
        $this->db->select("(SELECT jwb_temuan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_temuan");
        $this->db->select("(SELECT jwb_hasil from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_hasil");
        $this->db->select("(SELECT jwb_jawaban from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_jawaban");
        $this->db->from('audit au');
        $this->db->join('detailform dt', 'dt.form_id = au.form_id', 'left');
        $this->db->where('au.audit_id',$id);
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('au.auditee_id',$users_id);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->select("audit_id");
        $this->db->select("audit_status");
        $this->db->select("dt.dtform_id as dtform_id");
        $this->db->select("dt.dtform_pertanyaan as dtform_pertanyaan");
        $this->db->select("dt.dtform_lingkup as dtform_lingkup");
        $this->db->select("(SELECT jwb_catatan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_catatan");
        $this->db->select("(SELECT jwb_temuan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_temuan");
        $this->db->select("(SELECT jwb_hasil from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_hasil");
        $this->db->select("(SELECT jwb_jawaban from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id) as jwb_jawaban");
        $this->db->from('audit au');
        $this->db->join('detailform dt', 'dt.form_id = au.form_id', 'left');
        $this->db->where('au.audit_id',$id);
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('au.auditee_id',$users_id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id) {
        $this->db->from('audit');
        $this->db->where('audit.audit_id',$id);
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('audit.auditee_id',$users_id);
        }
        return $this->db->count_all_results();
    }

    public function add($data) {
        $this->db->insert('auditjawab',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('jwb_id',$id);
        $this->db->from('auditjawab');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

     public function jawab($data) {
        $this->db->trans_start();
        $this->db->where("audit_id",$data['audit_id']);
        $this->db->where("dtform_id",$data['dtform_id']);
        $this->db->update('auditjawab',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function is_exist($data) {
        
        $this->db->where("audit_id",$data['audit_id']);
        $this->db->where("dtform_id",$data['dtform_id']);
        $query = $this->db->get('auditjawab');
        if ($query->num_rows() > 0) {
            return true; // data ada
        } else {
            return false; // data tidak ada
        }
    }

    

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("jwb_id",$data['jwb_id']);
        $this->db->update('auditjawab',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
