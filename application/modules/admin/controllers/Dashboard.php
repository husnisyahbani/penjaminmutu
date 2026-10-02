<?php

/**
 * Dashboard
 *
 * Ringkasan statistik penjaminan mutu:
 *  - kartu angka utama di bagian atas,
 *  - grafik batang dokumen mutu per kategori (siklus PPEPP) di tengah,
 *  - statistik pendukung di sisi kiri dan kanan.
 */
class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/global/vendor/chart-js/Chart.bundle.min.js"));
        $this->load->js(base_url("assets/app/admin/dashboard.js?v=1.0.0"));

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function index()
    {
        $kategori = $this->dokumen_per_kategori();

        $this->data['content'] = 'dashboard';
        $this->data['title'] = 'Dashboard';
        $this->data['js'] = $this->load->get_js_files();
        $this->data['dashboard'] = 'active';

        $audit = $this->status_audit();

        $this->data['stat_utama'] = $this->ringkasan();
        $this->data['stat_kategori'] = $kategori;
        $this->data['stat_audit'] = $audit;
        $this->data['grafik_audit'] = $this->data_grafik_audit($audit);
        $this->data['stat_pengguna'] = $this->pengguna_per_peran();
        $this->data['stat_dokumen'] = $this->status_dokumen();
        $this->data['dokumen_terbaru'] = $this->dokumen_terbaru();

        $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
        $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');

        $this->template($this->data, $this->module);
    }

    /* =====================================================================
     | PENGAMBILAN DATA
     |
     | Catatan: nama variabel view diberi awalan "stat_" agar tidak bentrok
     | dengan penanda menu aktif pada includes/sidemenu.php
     | (mis. $audit dipakai untuk menu Audit).
     * ================================================================== */

    /**
     * Angka utama untuk kartu statistik bagian atas.
     */
    private function ringkasan()
    {
        $this->load->model('DataModel', 'datamodel');
        $this->load->model('MutuauditModel', 'mutuauditmodel');
        $this->load->model('SkModel', 'skmodel');
        $this->load->model('BeritaModel', 'beritamodel');
        $this->load->model('PengumumanModel', 'pengumumanmodel');

        $dokumen   = (int) $this->datamodel->count_all(NULL);
        $audit     = (int) $this->mutuauditmodel->count_all();
        $sk        = (int) $this->skmodel->count_all();
        $berita    = (int) $this->beritamodel->count_all();
        $pengumuman = (int) $this->pengumumanmodel->count_all();

        return array(
            'dokumen'    => $dokumen,
            'audit'      => $audit,
            'sk'         => $sk,
            'berita'     => $berita,
            'pengumuman' => $pengumuman,
            'publikasi'  => $berita + $pengumuman,
            'pengguna'   => (int) $this->db->count_all('users'),
        );
    }

    /**
     * Status audit mutu internal (draft, terkirim, proses, selesai).
     */
    private function status_audit()
    {
        $this->load->model('MutuauditModel', 'mutuauditmodel');

        return array(
            'draft'    => array('label' => 'Draft',    'jumlah' => (int) $this->mutuauditmodel->totalDraft(),    'warna' => 'default', 'hex' => '#90a4ae'),
            'terkirim' => array('label' => 'Terkirim', 'jumlah' => (int) $this->mutuauditmodel->totalTerkirim(), 'warna' => 'info',    'hex' => '#1e88e5'),
            'proses'   => array('label' => 'Proses',   'jumlah' => (int) $this->mutuauditmodel->totalProses(),   'warna' => 'warning', 'hex' => '#fb8c00'),
            'selesai'  => array('label' => 'Selesai',  'jumlah' => (int) $this->mutuauditmodel->totalSelesai(),  'warna' => 'success', 'hex' => '#43a047'),
        );
    }

    /**
     * Urutan & warna kategori dokumen (siklus PPEPP).
     */
    private function kategori_blueprint()
    {
        return array(
            'PENETAPAN'    => array('label' => 'Penetapan',    'warna' => '#3f51b5'),
            'PELAKSANAAN'  => array('label' => 'Pelaksanaan',  'warna' => '#009688'),
            'EVALUASI'     => array('label' => 'Evaluasi',     'warna' => '#ff9800'),
            'PENGENDALIAN' => array('label' => 'Pengendalian', 'warna' => '#e91e63'),
            'PENINGKATAN'  => array('label' => 'Peningkatan',  'warna' => '#8bc34a'),
        );
    }

    /**
     * Jumlah dokumen mutu per kategori (satu query, lalu dipetakan).
     */
    private function dokumen_per_kategori()
    {
        $out = array();

        foreach ($this->kategori_blueprint() as $kunci => $info) {
            $info['jumlah'] = 0;
            $out[$kunci] = $info;
        }

        $out['LAINNYA'] = array('label' => 'Lainnya / Informasi', 'warna' => '#90a4ae', 'jumlah' => 0);

        $rows = $this->db->select('data_kategori, COUNT(*) AS jumlah')
            ->from('data')
            ->group_by('data_kategori')
            ->get()->result_array();

        foreach ($rows as $row) {
            $kunci = strtoupper(trim($row['data_kategori']));
            $tujuan = isset($out[$kunci]) ? $kunci : 'LAINNYA';
            $out[$tujuan]['jumlah'] += (int) $row['jumlah'];
        }

        return $out;
    }

    /**
     * Data grafik batang status audit mutu internal (label, nilai, warna)
     * untuk Chart.js.
     */
    private function data_grafik_audit($audit)
    {
        $grafik = array(
            'label'  => array(),
            'nilai'  => array(),
            'warna'  => array(),
            'satuan' => 'audit',
        );

        foreach ($audit as $bagian) {
            $grafik['label'][] = $bagian['label'];
            $grafik['nilai'][] = $bagian['jumlah'];
            $grafik['warna'][] = $bagian['hex'];
        }

        return $grafik;
    }

    /**
     * Lima dokumen mutu terbaru untuk panel samping.
     */
    private function dokumen_terbaru()
    {
        return $this->db->select('data_uraian, data_kategori, data_create')
            ->from('data')
            ->order_by('data_create', 'DESC')
            ->limit(5)
            ->get()->result_array();
    }

    /**
     * Jumlah pengguna per peran.
     */
    private function pengguna_per_peran()
    {
        $out = array('PPM' => 0, 'AUDITOR' => 0, 'AUDITEE' => 0);

        $rows = $this->db->select('role, COUNT(*) AS jumlah')
            ->from('users')
            ->group_by('role')
            ->get()->result_array();

        foreach ($rows as $row) {
            $out[strtoupper(trim($row['role']))] = (int) $row['jumlah'];
        }

        return $out;
    }

    /**
     * Dokumen mutu yang ditampilkan / disembunyikan di halaman depan.
     */
    private function status_dokumen()
    {
        $total = (int) $this->db->count_all('data');

        $tampil = (int) $this->db->from('data')
            ->where('isshow', 1)
            ->count_all_results();

        return array(
            'tampil'  => $tampil,
            'sembunyi' => $total - $tampil,
            'total'   => $total,
        );
    }
}
