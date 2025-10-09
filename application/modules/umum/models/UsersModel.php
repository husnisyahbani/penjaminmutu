<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UsersModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function isAkunExists() {
        $email = $this->security->xss_clean($this->input->post('email'));
        $password = $this->security->xss_clean($this->input->post('password'));
        $this->db->where('email', $email);
        // Run the query
        $query = $this->db->get('users');
        if ($query->num_rows() == 1) {
            return true;
        } else {
            $data = array(
                'email' => $email,
                'password' => md5($password),
                'role' => 'MURID',
                'verifikasi' => 'NOT VALID'
            );
            $this->db->insert("users", $data);
            $insert_id = $this->db->insert_id();

            $this->load->library('email');
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->to($email);
            $this->email->from('admin@sman17plg.ac.id', 'SMA N 17 PALEMBANG');
            $this->email->subject('VERIFIKASI EMAIL');
            $this->email->message('<p>Klik link berikut ini untuk verifikasi email<a href="' . base_url() . "verifikasi?users=" . md5($insert_id) . '"> Klik Disini</a></p>');
            $this->email->send();
            return false;
        }
    }

    
    
    public function verifikasi($user) {
        $data = array(
            'verifikasi' => 'VALID'
        );

        $this->db->where('md5(users_id)', $user['users']);
        $this->db->trans_start();
        $this->db->update('users', $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    function login($data) {
        $this->db->from('mutu_users');
        $this->db->where('username', $data['username']);
        $this->db->where('password', MD5($data['password']));
        $query = $this->db->get();
        // Let's check if there are any results
        if ($query->num_rows() == 1 ) {
            $row = $query->row();
            $data = array(
                'users_id' => $row->users_id,
                'username' => $row->username,
                'role' => $row->role
            );
            $this->session->set_userdata($data);
            return true;
        } else {
            return false;
        }
    }
    
    

}
