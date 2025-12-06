<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AuditJawabDetailModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function getAuditJawabDetail($jwb_id) {
        $this->db->where("jwb_id",$jwb_id);
        $query = $this->db->get('auditjawabdetail');
        return $query->result_array();
    }

    

}
