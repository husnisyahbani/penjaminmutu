<?php

class Akun extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/akun.js?v=1.1"));
        $this->load->model('AkunModel', 'akun');

        $role = $this->session->userdata('role');
        $asli = $this->session->userdata('impersonator');
        $aksi = $this->router->fetch_method();

        // Saat admin sedang "login sebagai" user lain, sesi hanya boleh memakai
        // method kembali() untuk mengembalikan sesi admin semula.
        if ($asli && $aksi === 'kembali') {
            return;
        }

        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    /**
     * Halaman awal tiap peran (sama dengan pengalihan pada umum/Login).
     */
    private function halaman_peran($role)
    {
        $role = strtoupper(trim($role));

        if ($role === 'AUDITOR') {
            return base_url('auditor');
        }

        if ($role === 'AUDITEE') {
            return base_url('auditee');
        }

        return base_url('admin');
    }

    /* =====================================================================
     | LOGIN SEBAGAI USER (IMPERSONASI)
     * ================================================================== */

    /**
     * Masuk memakai akun user terpilih tanpa perlu password.
     * Identitas admin disimpan di session (key "impersonator") agar dapat
     * dikembalikan melalui kembali().
     */
    public function loginsebagai()
    {
        $this->output->set_content_type('application/json');

        $id = $this->input->post('id');
        $akun = $this->akun->getAkunById($id);

        if (empty($akun)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Akun tidak ditemukan.'));
            return;
        }

        if ((int) $akun['users_id'] === (int) $this->session->userdata('users_id')) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Anda sudah masuk sebagai akun ini.'));
            return;
        }

        /* Akun yang dinonaktifkan tidak boleh dipakai, termasuk lewat
           fitur "login sebagai". */
        if ($this->akun->statusSiap() && isset($akun['valid']) && (int) $akun['valid'] !== 1) {
            echo json_encode(array(
                'status' => FALSE,
                'pesan'  => 'Akun ' . $akun['nama'] . ' tidak aktif, sehingga tidak dapat dipakai.',
            ));
            return;
        }

        // simpan identitas admin asli (hanya sekali, agar tidak menumpuk)
        if (!$this->session->userdata('impersonator')) {
            $this->session->set_userdata('impersonator', array(
                'users_id' => $this->session->userdata('users_id'),
                'username' => $this->session->userdata('username'),
                'nama'     => $this->session->userdata('nama'),
                'role'     => $this->session->userdata('role'),
            ));
        }

        $this->session->set_userdata(array(
            'users_id' => $akun['users_id'],
            'username' => $akun['username'],
            'nama'     => $akun['nama'],
            'role'     => $akun['role'],
        ));

        echo json_encode(array(
            'status'   => TRUE,
            'pesan'    => 'Berhasil masuk sebagai ' . $akun['nama'] . '.',
            'nama'     => $akun['nama'],
            'role'     => $akun['role'],
            'redirect' => $this->halaman_peran($akun['role']),
        ));
    }

    /**
     * Kembali ke sesi admin semula setelah "login sebagai" user.
     */
    public function kembali()
    {
        $asli = $this->session->userdata('impersonator');

        if (!$asli) {
            redirect(base_url('admin/akun'));
        }

        $this->session->unset_userdata('impersonator');
        $this->session->set_userdata(array(
            'users_id' => $asli['users_id'],
            'username' => $asli['username'],
            'role'     => $asli['role'],
        ));

        // kembalikan nama admin (bila ada)
        if (!empty($asli['nama'])) {
            $this->session->set_userdata('nama', $asli['nama']);
        } else {
            $this->session->unset_userdata('nama');
        }

        $this->session->set_flashdata('pesanberhasil', 'Kembali masuk sebagai ' . $asli['username'] . '.');
        redirect(base_url('admin/akun'));
    }

    public function index() {
        $this->data['akun'] = 'active';
        $this->data['content'] = 'akun';
        $this->data['title'] = 'Akun';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    public function password() {
        $submitpass = $this->input->post('submitpass');
        if (isset($submitpass)) {
           $data = array();
           $passlama = md5($this->input->post('passlama'));
           
           $data['users_id'] = $this->session->userdata('users_id');
           $data['username'] = $this->session->userdata('username');
           $data['password'] = md5($this->input->post('password'));
           if($this->akun->passlama($passlama)&&$this->akun->edit($data)){
               $msg = 'Berhasil';
               $this->session->set_flashdata('pesanberhasil', $msg);
           }else{
               $msg = 'Gagal, password lama salah';
               $this->session->set_flashdata('pesanerror', $msg);
           }
           redirect(base_url($this->module));
       } else {
           $this->data['content'] = 'password';
           $this->data['title'] = 'Ubah Password';
           $this->data['js'] = $this->load->get_js_files();
           $this->template($this->data, $this->module);
       }
   }

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    /**
     * Hapus akun secara PERMANEN (barisnya dibuang dari tabel users).
     *
     * Untuk sekadar menutup akses tanpa membuang data, pakai tombol
     * nonaktifkan (endpoint status): akun tidak dapat masuk lagi, tetapi
     * data audit yang lalu tetap utuh.
     */
    public function hapus() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $id = (int) $this->input->post('id');

        if ($id < 1) {
            echo json_encode(array('status' => false, 'pesan' => 'Akun tidak dikenal.'));
            return;
        }

        /* Penjagaan: akun yang sedang dipakai tidak boleh menghapus dirinya. */
        if ($id === (int) $this->session->userdata('users_id')) {
            echo json_encode(array(
                'status' => false,
                'pesan'  => 'Akun Anda sendiri tidak dapat dihapus.',
            ));
            return;
        }

        $akun = $this->akun->getAkunById($id);
        if (empty($akun)) {
            echo json_encode(array('status' => false, 'pesan' => 'Akun tidak ditemukan.'));
            return;
        }

        $hasil = $this->akun->hapus($id);
        echo json_encode(array(
            'status' => $hasil,
            'pesan'  => $hasil ? 'Akun dihapus permanen.' : 'Akun gagal dihapus.',
        ));
    }

    /**
     * Aktifkan / nonaktifkan satu akun - pengganti form edit pada laman Akun.
     *
     * Kiriman: id (users_id) dan aktif (1 = aktifkan, 0 = nonaktifkan).
     * Akun yang dinonaktifkan tidak dapat masuk ke aplikasi dan tidak
     * ditawarkan lagi pada pilihan auditor/auditee.
     */
    public function status() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $id    = (int) $this->input->post('id');
        $aktif = ((string) $this->input->post('aktif') === '1');

        if (!$this->akun->statusSiap()) {
            echo json_encode(array(
                'status' => false,
                'pesan'  => 'Kolom status (users.valid) belum tersedia pada database ini.',
            ));
            return;
        }

        $akun = $this->akun->getAkunById($id);
        if (empty($akun)) {
            echo json_encode(array('status' => false, 'pesan' => 'Akun tidak ditemukan.'));
            return;
        }

        /* Penjagaan: admin tidak boleh menonaktifkan akunnya sendiri
           (supaya tidak terkunci dari aplikasi). */
        if (!$aktif && $id === (int) $this->session->userdata('users_id')) {
            echo json_encode(array(
                'status' => false,
                'pesan'  => 'Akun Anda sendiri tidak dapat dinonaktifkan.',
            ));
            return;
        }

        /* Penjagaan: minimal satu akun PPM tetap aktif. */
        if (!$aktif && strtoupper(trim($akun['role'])) === 'PPM'
                && $this->akun->hitungAktif('PPM') <= 1) {
            echo json_encode(array(
                'status' => false,
                'pesan'  => 'Tidak dapat menonaktifkan: akun PPM ini yang terakhir masih aktif.',
            ));
            return;
        }

        $hasil = $this->akun->ubahStatus($id, $aktif);
        echo json_encode(array(
            'status' => $hasil,
            'pesan'  => $hasil
                ? ($aktif ? 'Akun diaktifkan kembali.' : 'Akun dinonaktifkan.')
                : 'Status akun gagal diubah.',
        ));
    }
    
    public function reset() {
        $id = $this->input->post('id');
        $this->akun->reset($id);
    }

    public function tambah() {
        $submitakun = $this->input->post('submitakun');
        if (isset($submitakun)) {
            $data = array();
            $data['nama'] = $this->input->post('nama');
            $data['unitkerja'] = $this->input->post('unitkerja');
            $data['username'] = $this->input->post('username');
            $data['password'] = md5($this->input->post('password'));
            $data['role'] = $this->input->post('role');
            if(!$this->akun->is_exist($data) && $this->akun->add($data)){
                $msg = 'Berhasil';
                $this->session->set_flashdata('pesanberhasil', $msg);
            }else{
                $msg = 'Gagal, Data Username telah ada';
                $this->session->set_flashdata('pesanerror', $msg);
            }
            redirect(base_url($this->module.'/akun'));
        } else {
            $this->data['content'] = 'add_akun';
            $this->data['title'] = 'Akun';
            $this->data['akun'] = 'active';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module);
        }
    }

    public function listakun() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->akun->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->nama;
            $row[] = $field->unitkerja;
            $row[] = $field->username;
            $row[] = $field->password;
            $row[] = $field->role;

            /* Status akun: Aktif / Tidak Aktif (kolom users.valid). Bila
               kolomnya belum tersedia, seluruh akun dianggap aktif dan kolom
               status tidak ditampilkan. */
            $status_siap = $this->akun->statusSiap();
            $aktif       = TRUE;

            if ($status_siap) {
                $aktif = (!isset($field->valid) || (int) $field->valid === 1);
            }

            /* Kolom status selalu dikirim supaya jumlah kolom tabel tetap
               sama (bila kolom users.valid belum ada, semua akun Aktif). */
            $row[] = '<span class="badge ' . ($aktif ? 'badge-success' : 'badge-danger') . '">'
                . ($aktif ? 'Aktif' : 'Tidak Aktif') . '</span>';

            /* ---- kolom Aksi ----
               login sebagai | reset password | aktif/nonaktif | hapus permanen.
               Tombol "edit" lama diganti tombol status: pada laman ini yang
               diubah hanya status aktif akunnya. */
            $nama_akun = htmlspecialchars($field->nama, ENT_QUOTES);

            $aksi = '<button class="login-as btn btn-sm btn-icon btn-pure btn-primary on-default"'
                      . ' data-toggle="tooltip" data-original-title="Login sebagai user ini"'
                      . ' data-nama="' . $nama_akun . '"'
                      . ' data-username="' . htmlspecialchars($field->username, ENT_QUOTES) . '"'
                      . ' data-role="' . htmlspecialchars($field->role, ENT_QUOTES) . '"'
                      . ' id=' . $field->users_id . '><i class="icon md-account-add" aria-hidden="true"></i></button>';

            $aksi .= '<button class="reset btn btn-sm btn-icon btn-pure btn-default on-default reset-row"'
                      . ' data-toggle="tooltip" data-original-title="Reset password"'
                      . ' id=' . $field->users_id . '><i class="icon md-refresh" aria-hidden="true"></i></button>';

            if ($status_siap) {
                /* Sedang aktif -> tombol menonaktifkan, dan sebaliknya. */
                $aksi .= '<button class="status btn btn-sm btn-icon btn-pure btn-default on-default"'
                      . ' data-toggle="tooltip" data-original-title="'
                      . ($aktif ? 'Nonaktifkan akun ini' : 'Aktifkan kembali akun ini') . '"'
                      . ' data-aktif="' . ($aktif ? '1' : '0') . '"'
                      . ' data-nama="' . $nama_akun . '"'
                      . ' id=' . $field->users_id . '><i class="icon '
                      . ($aktif ? 'md-block' : 'md-check-circle') . '" aria-hidden="true"></i></button>';
            }

            $aksi .= '<button class="delete btn btn-sm btn-icon btn-pure btn-default on-default remove-row"'
                      . ' data-toggle="tooltip" data-original-title="Hapus permanen"'
                      . ' data-nama="' . $nama_akun . '"'
                      . ' id=' . $field->users_id . '><i class="icon md-delete" aria-hidden="true"></i></button>';

            $row[] = $aksi;
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->akun->count_all(),
            "recordsFiltered" => $this->akun->count_filtered($post['search'], $post['order']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
    
    public function kota($param = 0) {
        $this->db->select('id');
        $this->db->select('name');
        $this->db->from('regencies');
        $this->db->where('province_id', $param);
        $query = $this->db->get();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query->result());
    }

}
