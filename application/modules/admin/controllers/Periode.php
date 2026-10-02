<?php

/**
 * Periode Audit Mutu Internal (menu: Audit > Periode).
 *
 * Hanya role PPM (admin) yang boleh mengelola.
 */
class Periode extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/periode.js?v=1.0"));
        // Informasi tombol aksi saat hover (lihat assets/app/tabel-aksi.css)
        $this->load->js(base_url("assets/app/tabel-aksi.js?v=1.0"));
        $this->load->model('PeriodeModel', 'periode');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index() {
        $this->data['content']  = 'periode/index';
        $this->data['title']    = 'Periode Audit';
        $this->data['js']       = $this->load->get_js_files();
        $this->data['auditmenu'] = 'active';
        $this->data['periode']  = 'active';   // penanda menu aktif (bukan daftar data)
        $this->data['terpasang'] = $this->periode->installed();
        $this->data['tahun']     = $this->periode->pilihanTahun();
        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
        $this->template($this->data, $this->module);
    }

    /** Siapkan tabel periode + kolom relasi pada tabel audit. */
    public function pasang() {
        $this->output->set_content_type('application/json');

        try {
            $this->periode->install();
            $status = array('status' => TRUE, 'pesan' => 'Tabel periode berhasil disiapkan.');
        } catch (Exception $e) {
            $status = array('status' => FALSE, 'pesan' => 'Gagal menyiapkan tabel: ' . $e->getMessage());
        }

        echo json_encode($status);
    }

    /* =====================================================================
     | DAFTAR PERIODE
     * ================================================================== */

    public function listperiode() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order']  = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start']  = $this->input->post('start');
        $post['draw']   = $this->input->post('draw');

        $list = $this->periode->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');

        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->periode_tahun;
            $row[] = date('d-m-Y', strtotime($field->periode_mulai));
            $row[] = date('d-m-Y', strtotime($field->periode_selesai));

            // ---- kolom Status: badge, tanpa tombol ----
            $row[] = $field->periode_aktif
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-default">Tidak aktif</span>';

            // ---- kolom Aksi: tombol seragam (ikon + keterangan saat hover) ----
            $btn_aktif = '';
            if (!$field->periode_aktif) {
                $btn_aktif = '<button type="button" class="aktifkan btn btn-sm btn-icon btn-success" '
                    . 'data-info="Jadikan periode aktif" aria-label="Set aktif" id="' . $field->periode_id . '">'
                    . '<i class="icon md-check-circle" aria-hidden="true"></i></button>';
            } else {
                // Periode yang sedang aktif dapat dibatalkan kembali.
                $btn_aktif = '<button type="button" class="batalkan btn btn-sm btn-icon btn-warning" '
                    . 'data-info="Batalkan status aktif periode ini" aria-label="Batalkan aktif" id="' . $field->periode_id . '">'
                    . '<i class="icon md-close" aria-hidden="true"></i></button>';
            }

            $btn_edit = '<button type="button" class="edit btn btn-sm btn-icon btn-primary" '
                . 'data-info="Ubah periode ini" aria-label="Edit" id="' . $field->periode_id . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button>';

            $btn_hapus = '<button type="button" class="delete btn btn-sm btn-icon btn-danger" '
                . 'data-info="Hapus periode ini" aria-label="Hapus" id="' . $field->periode_id . '">'
                . '<i class="icon md-delete" aria-hidden="true"></i></button>';

            $row[] = '<div class="tabel-aksi">' . $btn_aktif . $btn_edit . $btn_hapus . '</div>';

            $data[] = $row;
        }

        $output = array(
            "draw"            => $post['draw'],
            "recordsTotal"    => $this->periode->count_all(),
            "recordsFiltered" => $this->periode->count_filtered($post['search'], $post['order']),
            "data"            => $data,
        );
        echo json_encode($output);
    }

    /* Nama method diawali 'get_' karena MY_Controller sudah memakai GET(). */
    public function get_periode($id = null) {
        $this->output->set_content_type('application/json');

        $row = $this->periode->getById($id);
        if ($row) {
            $row['status'] = TRUE;
            echo json_encode($row);
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode tidak ditemukan'));
        }
    }

    /** Validasi masukan formulir periode. */
    private function _masukan() {
        $data = array();
        $data['periode_tahun']   = trim($this->input->post('periode_tahun'));
        $data['periode_mulai']   = trim($this->input->post('periode_mulai'));
        $data['periode_selesai'] = trim($this->input->post('periode_selesai'));

        if ($data['periode_tahun'] === '' || $data['periode_mulai'] === '' || $data['periode_selesai'] === '') {
            return array(FALSE, 'Tahun, tanggal mulai, dan tanggal berakhir wajib diisi.');
        }

        if (strtotime($data['periode_selesai']) < strtotime($data['periode_mulai'])) {
            return array(FALSE, 'Tanggal berakhir tidak boleh lebih awal dari tanggal mulai.');
        }

        return array(TRUE, $data);
    }

    public function tambah() {
        $this->output->set_content_type('application/json');

        list($ok, $data) = $this->_masukan();
        if (!$ok) {
            echo json_encode(array('status' => FALSE, 'pesan' => $data));
            return;
        }

        if ($this->periode->add($data)) {
            echo json_encode(array('status' => TRUE, 'pesan' => 'Periode berhasil disimpan.'));
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode gagal disimpan.'));
        }
    }

    public function edit() {
        $this->output->set_content_type('application/json');

        $id = (int) $this->input->post('periode_id');
        if (empty($id)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode tidak diketahui.'));
            return;
        }

        list($ok, $data) = $this->_masukan();
        if (!$ok) {
            echo json_encode(array('status' => FALSE, 'pesan' => $data));
            return;
        }
        $data['periode_id'] = $id;

        if ($this->periode->edit($data)) {
            echo json_encode(array('status' => TRUE, 'pesan' => 'Periode berhasil diperbarui.'));
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode gagal diperbarui.'));
        }
    }

    /** Set aktif; kirim id = 0 untuk menonaktifkan semua periode. */
    public function aktifkan() {
        $this->output->set_content_type('application/json');

        $id = (int) $this->input->post('id');

        if ($id > 0 && !$this->periode->getById($id)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode tidak ditemukan.'));
            return;
        }

        if ($this->periode->setAktif($id)) {
            $pesan = $id > 0
                ? 'Periode berhasil diaktifkan.'
                : 'Semua periode dinonaktifkan; Daftar Audit menampilkan seluruh data.';
            echo json_encode(array('status' => TRUE, 'pesan' => $pesan));
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode gagal diaktifkan.'));
        }
    }

    /**
     * Batalkan status aktif sebuah periode.
     * Karena hanya satu periode yang boleh aktif, membatalkan berarti
     * tidak ada periode aktif dan Daftar Audit menampilkan seluruh data.
     */
    public function nonaktifkan() {
        $this->output->set_content_type('application/json');

        $id = (int) $this->input->post('id');
        if (empty($id)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode tidak diketahui.'));
            return;
        }

        $periode = $this->periode->getById($id);
        if (!$periode) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode tidak ditemukan.'));
            return;
        }

        if (!$periode['periode_aktif']) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode ini tidak sedang aktif.'));
            return;
        }

        if ($this->periode->setAktif(0)) {
            echo json_encode(array(
                'status' => TRUE,
                'pesan'  => 'Status aktif periode dibatalkan; Daftar Audit kini menampilkan seluruh data.',
            ));
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Status aktif gagal dibatalkan.'));
        }
    }

    public function hapus() {
        $this->output->set_content_type('application/json');

        $id = (int) $this->input->post('id');
        if (empty($id)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode tidak diketahui.'));
            return;
        }

        // Periode yang masih dipakai audit tidak boleh dihapus supaya
        // data audit tidak kehilangan periodenya.
        $dipakai = $this->periode->jumlahAudit($id);
        if ($dipakai > 0) {
            echo json_encode(array(
                'status' => FALSE,
                'pesan'  => 'Periode tidak dapat dihapus karena masih dipakai oleh ' . $dipakai . ' audit.',
            ));
            return;
        }

        // Begitu pula formulir: formulir tidak dihapus dan tidak dilepas
        // diam-diam dari periodenya, jadi periode ini ditahan sampai
        // formulir dipindahkan ke periode lain.
        if ($this->periode->formulirSiap()) {
            $formulir = $this->periode->jumlahFormulir($id);
            if ($formulir > 0) {
                echo json_encode(array(
                    'status' => FALSE,
                    'pesan'  => 'Periode tidak dapat dihapus karena masih dipakai oleh ' . $formulir . ' formulir. Pindahkan formulir tersebut ke periode lain terlebih dahulu.',
                ));
                return;
            }
        }

        if ($this->periode->hapus($id)) {
            echo json_encode(array('status' => TRUE, 'pesan' => 'Periode berhasil dihapus.'));
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Periode gagal dihapus.'));
        }
    }
}
