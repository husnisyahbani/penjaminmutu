<?php

/**
 * Pengaturan Home
 *
 * CRUD pengaturan tampilan halaman depan (menu: Website > Pengaturan Home).
 * Terdiri dari dua jenis data:
 *  - pengaturan  : nilai tunggal (judul, teks, gambar, warna, tombol)
 *  - item        : konten berulang (kartu akses, galeri, misi, tupoksi, sasaran)
 */
class Pengaturanhome extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/pengaturanhome.js?v=1.0.0"));
        $this->load->model('HomeModel', 'homemodel');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    /* =====================================================================
     | HALAMAN UTAMA
     * ================================================================== */

    public function index()
    {
        $this->data['content'] = 'pengaturanhome/index';
        $this->data['title'] = 'Pengaturan Home';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['pengaturanhome'] = 'active';
        $this->data['website'] = 'active';

        $this->data['terpasang'] = $this->homemodel->installed();
        $this->data['tabs'] = $this->tabs();
        $this->data['grup_pengaturan'] = $this->homemodel->get_settings_grouped();
        $this->data['grup_item'] = $this->homemodel->item_blueprint();
        $this->data['items'] = $this->semua_item();

        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');

        $this->template($this->data, $this->module);
    }

    /**
     * Susunan tab halaman pengaturan.
     */
    private function tabs()
    {
        return array(
            array('id' => 'identitas', 'label' => 'Identitas', 'ikon' => 'md-view-compact', 'pengaturan' => array('umum'), 'item' => array()),
            array('id' => 'hero', 'label' => 'Hero', 'ikon' => 'md-image', 'pengaturan' => array('hero'), 'item' => array()),
            array('id' => 'akses', 'label' => 'Kartu Akses', 'ikon' => 'md-account-box', 'pengaturan' => array('akses'), 'item' => array('kartu')),
            array('id' => 'profil', 'label' => 'Profil & Galeri', 'ikon' => 'md-info-outline', 'pengaturan' => array('profil'), 'item' => array('slider')),
            array('id' => 'visimisi', 'label' => 'Visi & Misi', 'ikon' => 'md-eye', 'pengaturan' => array('visi'), 'item' => array('misi')),
            array('id' => 'tupoksi', 'label' => 'Tupoksi', 'ikon' => 'md-assignment', 'pengaturan' => array('tupoksi'), 'item' => array('tupoksi')),
            array('id' => 'sasaran', 'label' => 'Sasaran Mutu', 'ikon' => 'md-flag', 'pengaturan' => array('sasaran'), 'item' => array('sasaran')),
            array('id' => 'organisasi', 'label' => 'Pengelola & Struktur', 'ikon' => 'md-accounts', 'pengaturan' => array('pengelola', 'struktur'), 'item' => array()),
            array('id' => 'informasi', 'label' => 'SK, Berita & Pengumuman', 'ikon' => 'md-file-text', 'pengaturan' => array('dokumen', 'berita', 'pengumuman'), 'item' => array()),
            array('id' => 'kontak', 'label' => 'Kontak & Footer', 'ikon' => 'md-phone', 'pengaturan' => array('kontak'), 'item' => array()),
            array('id' => 'section', 'label' => 'Tampilkan Section', 'ikon' => 'md-eye-off', 'pengaturan' => array('section'), 'item' => array()),
        );
    }

    /**
     * Semua item dikelompokkan per grup (untuk tabel di tab).
     */
    private function semua_item()
    {
        $out = array();

        foreach ($this->homemodel->item_blueprint() as $grup => $bagian) {
            // halaman admin hanya menampilkan data yang tersimpan di database
            $out[$grup] = $this->homemodel->get_items($grup, FALSE, FALSE);
        }

        return $out;
    }

    /* =====================================================================
     | INSTALLASI TABEL
     * ================================================================== */

    /**
     * Buat tabel pengaturan home + isi nilai bawaan.
     */
    public function pasang()
    {
        $this->output->set_content_type('application/json');

        try {
            $this->homemodel->install();
            $status = array('status' => TRUE, 'pesan' => 'Tabel pengaturan home berhasil disiapkan.');
        } catch (Exception $e) {
            $status = array('status' => FALSE, 'pesan' => 'Gagal menyiapkan tabel: ' . $e->getMessage());
        }

        echo json_encode($status);
    }

    /* =====================================================================
     | PENGATURAN (nilai tunggal)
     * ================================================================== */

    /**
     * Simpan pengaturan dari satu tab.
     * Field: setting[kunci] = nilai  (gambar dikirim sebagai file_<kunci>)
     */
    public function simpan()
    {
        $this->output->set_content_type('application/json');

        $kiriman = $this->input->post('setting');
        $kiriman = is_array($kiriman) ? $kiriman : array();

        $data = array();

        foreach ($kiriman as $kunci => $nilai) {
            $data[$kunci] = $this->bersihkan($kunci, $nilai);

            // gambar: upload bila ada berkas baru
            if ($this->homemodel->tipe_of($kunci) === 'image' && $this->ada_berkas('file_' . $kunci)) {
                $upload = $this->upload_gambar('file_' . $kunci);

                if ($upload['status']) {
                    $data[$kunci] = $upload['file'];
                } else {
                    echo json_encode(array('status' => FALSE, 'pesan' => $upload['pesan']));
                    return;
                }
            }
        }

        if (empty($data)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Tidak ada data yang dikirim.'));
            return;
        }

        $this->homemodel->save_settings($data);

        echo json_encode(array('status' => TRUE, 'pesan' => 'Pengaturan berhasil disimpan.'));
    }

    /**
     * Bersihkan nilai sesuai tipe input.
     */
    private function bersihkan($kunci, $nilai)
    {
        $tipe = $this->homemodel->tipe_of($kunci);

        if ($tipe === 'toggle') {
            return ($nilai == '1') ? '1' : '0';
        }

        if ($tipe === 'color') {
            $nilai = trim($nilai);
            return preg_match('/^#[0-9a-f]{6}$/i', $nilai) ? $nilai : '';
        }

        if (in_array($tipe, array('text', 'image'), TRUE)) {
            return trim(strip_tags((string) $nilai));
        }

        // textarea / editor: biarkan tag dasar untuk penekanan teks
        return trim($nilai);
    }

    /* =====================================================================
     | ITEM (konten berulang)
     * ================================================================== */

    public function tambahitem()
    {
        $this->output->set_content_type('application/json');

        $grup = $this->input->post('item_grup');

        if (!$this->grup_item_valid($grup)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Grup konten tidak dikenal.'));
            return;
        }

        $data = $this->post_item($grup);
        $data['item_grup'] = $grup;

        $this->homemodel->add_item($data);

        echo json_encode(array('status' => TRUE, 'pesan' => 'Konten berhasil ditambahkan.'));
    }

    public function edititem()
    {
        $this->output->set_content_type('application/json');

        $item_id = (int) $this->input->post('item_id');
        $item = $this->homemodel->get_item($item_id);

        if (!$item) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Konten tidak ditemukan.'));
            return;
        }

        $data = $this->post_item($item['item_grup']);
        $data['item_id'] = $item_id;

        $this->homemodel->edit_item($data);

        echo json_encode(array('status' => TRUE, 'pesan' => 'Konten berhasil diperbarui.'));
    }

    /**
     * Ambil satu item (untuk form edit).
     */
    public function getItem($id)
    {
        $this->output->set_content_type('application/json');

        $item = $this->homemodel->get_item($id);

        if (!$item) {
            echo json_encode(array('status' => FALSE));
            return;
        }

        $item['status'] = TRUE;
        echo json_encode($item);
    }

    public function hapusitem()
    {
        $this->output->set_content_type('application/json');

        $item_id = (int) $this->input->post('item_id');

        if ($this->homemodel->delete_item($item_id)) {
            echo json_encode(array('status' => TRUE, 'pesan' => 'Konten berhasil dihapus.'));
        } else {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Konten gagal dihapus.'));
        }
    }

    /**
     * Aktifkan / nonaktifkan sebuah item.
     */
    public function statusitem()
    {
        $this->output->set_content_type('application/json');

        $item_id = (int) $this->input->post('item_id');
        $item = $this->homemodel->get_item($item_id);

        if (!$item) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Konten tidak ditemukan.'));
            return;
        }

        $baru = ((int) $item['item_status'] === 1) ? 0 : 1;

        $this->homemodel->edit_item(array('item_id' => $item_id, 'item_status' => $baru));

        echo json_encode(array('status' => TRUE, 'pesan' => 'Status konten diperbarui.', 'item_status' => $baru));
    }

    /**
     * Susun data item dari input form.
     */
    private function post_item($grup)
    {
        $data = array(
            'item_judul'      => trim((string) $this->input->post('item_judul')),
            'item_isi'        => trim((string) $this->input->post('item_isi')),
            'item_ikon'       => trim((string) $this->input->post('item_ikon')),
            'item_link'       => trim((string) $this->input->post('item_link')),
            'item_label_link' => trim((string) $this->input->post('item_label_link')),
            'item_warna'      => trim((string) $this->input->post('item_warna')),
            'item_urutan'     => (int) $this->input->post('item_urutan'),
            'item_status'     => ($this->input->post('item_status') == '1') ? 1 : 0,
        );

        // gambar: pertahankan nilai lama bila tidak ada berkas baru
        $data['item_gambar'] = trim((string) $this->input->post('item_gambar_lama'));

        if ($this->input->post('hapus_gambar') == '1') {
            $data['item_gambar'] = '';
        }

        if ($this->ada_berkas('item_gambar')) {
            $upload = $this->upload_gambar('item_gambar');

            if ($upload['status']) {
                $data['item_gambar'] = $upload['file'];
            }
        }

        return $data;
    }

    private function grup_item_valid($grup)
    {
        $blueprint = $this->homemodel->item_blueprint();

        return isset($blueprint[$grup]);
    }

    /* =====================================================================
     | UPLOAD
     * ================================================================== */

    private function ada_berkas($field)
    {
        return isset($_FILES[$field]) && !empty($_FILES[$field]['name']);
    }

    /**
     * Upload gambar ke folder filedata (mengikuti pola controller lain).
     *
     * @return array status + nama berkas
     */
    private function upload_gambar($field)
    {
        $config = array(
            'upload_path'   => 'filedata',
            'allowed_types' => 'gif|jpg|jpeg|png|webp|svg',
            'encrypt_name'  => TRUE,
        );

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($field)) {
            return array('status' => FALSE, 'pesan' => strip_tags($this->upload->display_errors('', '')));
        }

        $berkas = $this->upload->data();

        return array('status' => TRUE, 'file' => 'filedata/' . $berkas['file_name']);
    }
}
