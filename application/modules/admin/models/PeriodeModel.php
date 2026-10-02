<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Periode Audit Mutu Internal.
 *
 * Tabel : mutu_periode (daftar periode: tahun, tanggal mulai, tanggal berakhir,
 *         penanda periode aktif)
 * Relasi: mutu_audit.periode_id    -> mutu_periode.periode_id
 *         mutu_formulir.periode_id -> mutu_periode.periode_id
 *
 * Satu periode saja yang boleh aktif; bila tidak ada periode aktif maka
 * Daftar Audit menampilkan seluruh data.
 */
class PeriodeModel extends CI_Model {

    var $tabel       = 'mutu_periode';
    var $t_audit     = 'mutu_audit';
    var $kol_audit   = 'periode_id';
    var $t_formulir  = 'mutu_formulir';
    var $kol_formulir = 'periode_id';

    var $column_search = array('periode_tahun', 'periode_mulai', 'periode_selesai');
    var $column_order  = array(null, 'periode_tahun', 'periode_mulai', 'periode_selesai', null, null);
    var $order         = array('periode_tahun' => 'DESC', 'periode_id' => 'DESC');

    function __construct() {
        parent::__construct();
    }

    /* =====================================================================
     | KESIAPAN TABEL
     * ================================================================== */

    /**
     * Apakah tabel periode sudah ada DAN kolom relasi pada mutu_audit ada.
     */
    public function installed() {
        return $this->db->table_exists($this->tabel)
            && $this->db->field_exists($this->kol_audit, $this->t_audit)
            && $this->formulirSiap();
    }

    /** Kolom relasi periode pada mutu_formulir sudah ada? */
    public function formulirSiap() {
        return $this->db->table_exists($this->t_formulir)
            && $this->db->field_exists($this->kol_formulir, $this->t_formulir);
    }

    /**
     * Buat tabel periode + kolom relasi pada tabel audit (aman dijalankan
     * berulangkali). Dipakai tombol "Buat Tabel Periode" atau otomatis
     * saat halaman periode dibuka bila belum siap.
     */
    public function install() {
        $this->load->dbforge();

        if (!$this->db->table_exists($this->tabel)) {
            $this->dbforge->add_field(array(
                'periode_id'      => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'periode_tahun'   => array('type' => 'VARCHAR', 'constraint' => 20),
                'periode_mulai'   => array('type' => 'DATE'),
                'periode_selesai' => array('type' => 'DATE'),
                'periode_aktif'   => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
                'periode_create'  => array('type' => 'DATETIME', 'null' => TRUE),
            ));
            $this->dbforge->add_key('periode_id', TRUE);
            $this->dbforge->create_table($this->tabel, TRUE);
        }

        // Catatan: DBForge::add_column() menambahkan dbprefix sendiri, jadi
        // nama tabel di sini harus tanpa prefix (lihat LingkupModel).
        if (!$this->db->field_exists($this->kol_audit, $this->t_audit)) {
            $this->dbforge->add_column('audit', array(
                $this->kol_audit => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
            ));
        }

        // Formulir audit juga berelasi ke periode (daftar formulir dapat
        // disaring per periode).
        if (!$this->formulirSiap()) {
            $this->dbforge->add_column('formulir', array(
                $this->kol_formulir => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
            ));
        }

        return $this->installed();
    }

    /* =====================================================================
     | DATA UNTUK TABEL (DataTables)
     * ================================================================== */

    private function _get_datatables_query($search, $ordering) {
        $i = 0;

        foreach ($this->column_search as $item) {
            if ($search['value']) {
                if ($i === 0) {
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

        if (isset($ordering) && isset($ordering[0]['column'])) {
            $this->db->order_by($this->column_order[$ordering[0]['column']], $ordering[0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($length, $start, $search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $this->db->from($this->tabel);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($search, $ordering) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->from($this->tabel);
        return $this->db->get()->num_rows();
    }

    function count_all() {
        return $this->db->count_all_results($this->tabel);
    }

    /* =====================================================================
     | DASAR
     * ================================================================== */

    /** Semua periode, terbaru lebih dahulu. */
    function getAll() {
        return $this->db->order_by('periode_tahun', 'DESC')
            ->order_by('periode_id', 'DESC')
            ->get($this->tabel)->result_array();
    }

    function getById($id) {
        return $this->db->where('periode_id', $id)->get($this->tabel)->row_array();
    }

    /** ID periode aktif, atau NULL bila tidak ada yang aktif. */
    function aktifId() {
        if (!$this->db->table_exists($this->tabel)) {
            return NULL;
        }

        $row = $this->db->where('periode_aktif', 1)
            ->order_by('periode_id', 'DESC')
            ->limit(1)
            ->get($this->tabel)->row_array();

        return isset($row['periode_id']) ? (int) $row['periode_id'] : NULL;
    }

    /** Jumlah audit yang memakai periode ini. */
    function jumlahAudit($id) {
        if (!$this->db->field_exists($this->kol_audit, $this->t_audit)) {
            return 0;
        }
        return $this->db->where($this->kol_audit, $id)->count_all_results($this->t_audit);
    }

    /** Jumlah formulir yang memakai periode ini. */
    function jumlahFormulir($id) {
        if (!$this->formulirSiap()) {
            return 0;
        }
        return $this->db->where($this->kol_formulir, $id)->count_all_results($this->t_formulir);
    }

    /* =====================================================================
     | PERUBAHAN DATA
     * ================================================================== */

    function add($data) {
        $data['periode_create'] = date('Y-m-d H:i:s');
        $this->db->insert($this->tabel, $data);
        return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
    }

    function edit($data) {
        $this->db->where('periode_id', $data['periode_id']);
        $this->db->update($this->tabel, $data);
        return ($this->db->affected_rows() >= 0) ? TRUE : FALSE;
    }

    function hapus($id) {
        $this->db->where('periode_id', $id);
        $this->db->delete($this->tabel);
        return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
    }

    /**
     * Tetapkan satu periode sebagai periode aktif; periode lain dimatikan.
     * Bila id = 0 / kosong, seluruh periode menjadi tidak aktif
     * (Daftar Audit akan menampilkan semua data).
     */
    function setAktif($id) {
        $this->db->trans_start();

        $this->db->update($this->tabel, array('periode_aktif' => 0));

        if (!empty($id)) {
            $this->db->where('periode_id', $id);
            $this->db->update($this->tabel, array('periode_aktif' => 1));
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /* =====================================================================
     | BANTUAN
     * ================================================================== */

    /**
     * Daftar tahun untuk pilihan pada formulir: tahun berjalan +- beberapa
     * tahun, ditambah tahun yang sudah dipakai data yang ada.
     */
    function pilihanTahun() {
        $tahun = range((int) date('Y') - 3, (int) date('Y') + 3);

        if ($this->db->table_exists($this->tabel)) {
            foreach ($this->db->select('periode_tahun')->get($this->tabel)->result_array() as $row) {
                if (is_numeric($row['periode_tahun'])) {
                    $tahun[] = (int) $row['periode_tahun'];
                }
            }
        }

        $tahun = array_unique($tahun);
        sort($tahun);
        return $tahun;
    }
}
