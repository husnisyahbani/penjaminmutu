<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SuratModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $table = 'surat';
    
    
    public function hapus($id) {
        $this->db->where("surat_id",$id);
        $this->db->from($this->table);
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function get_surat($id) {
        $this->db->where("surat.id_ip",$id);
        $this->db->from($this->table);
        $this->db->join('tandatangan', 'tandatangan.tandatangan_id = surat.tandatangan_id', 'left');
        $this->db->join('izinpenelitian', 'izinpenelitian.id_ip = surat.id_ip', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("surat_id",$data['surat_id']);
        $this->db->update($this->table,$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function sendEmail($data){
        //$emailsto = array_column($daftar, 'email');
        if(!empty($data['konsul_email'])){
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'smtp.zoho.com',
                'smtp_port' => '465',
                'smtp_user' => 'admin@kursma-disdikss.com',
                'smtp_pass' => 'Husni!@#',
                'smtp_crypto' => 'ssl',
                'mailtype' => 'html',
                'charset' => 'iso-8859-1'
            );
            
            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");
    
            $this->email->initialize($config);
            $this->email->from('admin@kursma-disdikss.com', 'Sistem Informasi Seksi Kurikulum SMA');
            $this->email->to($data['konsul_email']);
            $this->email->subject($data['konsul_judul']);
            $body = $this->load->view('emailformat.php',$data,TRUE);
            $this->email->message($body);
            //Send mail
            return $this->email->send();
        }      
        return false;    
    }

    
    public function add($data) {
        $this->db->insert($this->table,$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }
    

    
    

}
