<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PtkModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /* Satu baris = satu butir lingkup yang punya temuan (struktur baru). */
    var $column_search = array('f.form_nama','dt.dtform_pertanyaan','lg.lingkup_isi','jb.jwb_hasil','jb.jwb_temuan','jb.jwb_catatan','jb.jwb_koreksi');
    var $column_order = array(null,'f.form_nama','dt.dtform_pertanyaan','lg.lingkup_isi','jb.jwb_hasil','jb.jwb_temuan','jb.jwb_catatan','jb.jwb_koreksi');
    /* audit_id juga ada di tabel jawaban -> harus memakai alias. */
    var $order = array('au.audit_id' => 'asc');

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

    private function _query_ptk() {
        $this->db->select('au.audit_id, au.audit_status');
        $this->db->select('dt.dtform_id, dt.dtform_pertanyaan');
        $this->db->select('f.form_nama');
        $this->db->select('lg.lingkup_id, lg.lingkup_isi');
        $this->db->select('jb.jwb_id, jb.jwb_hasil, jb.jwb_temuan, jb.jwb_catatan, jb.jwb_koreksi');
        $this->db->from('audit au');
        $this->db->join('detailform dt', 'dt.form_id = au.form_id', 'inner');
        $this->db->join('formulir f', 'f.form_id = au.form_id', 'left');
        $this->db->join('lingkup lg', 'lg.dtform_id = dt.dtform_id', 'inner');
        $this->db->join('auditjawab jb', 'jb.audit_id = au.audit_id AND jb.lingkup_id = lg.lingkup_id', 'inner');
        $this->db->where("jb.jwb_temuan IN ('OB','TS MINOR','TS MAYOR')");

        $users_id = $this->session->userdata('users_id');
        if (isset($users_id)) {
            $this->db->where('au.auditee_id', $users_id);
        }
    }

    function get_datatables($length, $start, $search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $this->_query_ptk();
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->_query_ptk();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->_query_ptk();
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

     public function koreksi($data) {
        $this->db->trans_start();
        $this->db->where("audit_id",$data['audit_id']);
        if (!empty($data['lingkup_id'])) {
            // Struktur baru: koreksi menempel pada baris butir lingkup.
            $this->db->where("lingkup_id",$data['lingkup_id']);
        } else {
            $this->db->where("dtform_id",$data['dtform_id']);
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        }
        $this->db->update('auditjawab',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /** Butir tilik + nilai (untuk modal koreksi). */
    public function getButir($audit_id, $lingkup_id) {
        $this->_query_ptk();
        $this->db->where('au.audit_id', $audit_id);
        $this->db->where('lg.lingkup_id', $lingkup_id);
        return $this->db->get()->row_array();
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

    /**
     * Ringkasan temuan unit auditee ini dalam satu kueri:
     * total, jumlah per kategori temuan, dan jumlah yang belum punya
     * rencana koreksi (dipakai kartu statistik halaman PTK).
     */
    public function ringkasan() {
        $this->db->select('COUNT(*) AS total', FALSE);
        $this->db->select("SUM(CASE WHEN jwb_temuan = 'OB' THEN 1 ELSE 0 END) AS observasi", FALSE);
        $this->db->select("SUM(CASE WHEN jwb_temuan = 'TS MINOR' THEN 1 ELSE 0 END) AS minor", FALSE);
        $this->db->select("SUM(CASE WHEN jwb_temuan = 'TS MAYOR' THEN 1 ELSE 0 END) AS mayor", FALSE);
        $this->db->select("SUM(CASE WHEN (jwb_koreksi IS NULL OR TRIM(jwb_koreksi) = '') THEN 1 ELSE 0 END) AS tanpa_koreksi", FALSE);
        $this->db->from('auditjawab');
        $this->db->join('audit ma', 'ma.audit_id = auditjawab.audit_id', 'left');
        $users_id = $this->session->userdata('users_id');
        if (isset($users_id)) {
            $this->db->where('ma.auditee_id', $users_id);
        }
        $this->db->where('lingkup_id IS NOT NULL', NULL, FALSE);
        $this->db->where("jwb_temuan IN ('OB','TS MINOR','TS MAYOR')");

        $row = $this->db->get()->row_array();

        return array(
            'total'         => isset($row['total']) ? (int) $row['total'] : 0,
            'observasi'     => isset($row['observasi']) ? (int) $row['observasi'] : 0,
            'minor'         => isset($row['minor']) ? (int) $row['minor'] : 0,
            'mayor'         => isset($row['mayor']) ? (int) $row['mayor'] : 0,
            'tanpa_koreksi' => isset($row['tanpa_koreksi']) ? (int) $row['tanpa_koreksi'] : 0,
        );
    }

    public function totalObservasi(){
        $this->db->from('mutu_auditjawab');
        $this->db->join('mutu_audit ma', 'ma.audit_id = mutu_auditjawab.audit_id', 'left');
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('ma.auditee_id',$users_id);
        }
        $this->db->where('lingkup_id IS NOT NULL', NULL, FALSE);
        $this->db->where("jwb_temuan","OB");
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function totalMinor(){
        $this->db->from('mutu_auditjawab');
        $this->db->join('mutu_audit ma', 'ma.audit_id = mutu_auditjawab.audit_id', 'left');
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('ma.auditee_id',$users_id);
        }
        $this->db->where('lingkup_id IS NOT NULL', NULL, FALSE);
        $this->db->where("jwb_temuan","TS MINOR");
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function totalMayor(){
        $this->db->from('mutu_auditjawab');
        $this->db->join('mutu_audit ma', 'ma.audit_id = mutu_auditjawab.audit_id', 'left');
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('ma.auditee_id',$users_id);
        }
        $this->db->where('lingkup_id IS NOT NULL', NULL, FALSE);
        $this->db->where("jwb_temuan","TS MAYOR");
        $query = $this->db->get();
        return $query->num_rows();
    }

}
