<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Daftar tilik (delik) audit.
 *
 * Struktur baru: baris daftar tilik berasal dari butir lingkup pertanyaan
 * (tabel lingkup), sedangkan jawabannya disimpan pada auditjawab yang
 * berelasi lewat kolom lingkup_id:
 *
 *   auditjawab (lingkup_id NULL)      -> data pertanyaan: jawaban auditee, tujuan
 *   auditjawab (lingkup_id terisi)    -> data butir: hasil, temuan, catatan
 */
class DtjwbModel extends CI_Model {

    var $column_search = array('lg.lingkup_isi','jb.jwb_hasil','jb.jwb_temuan','jb.jwb_catatan');
    var $column_order = array(null,'lg.lingkup_urut','jb.jwb_hasil','jb.jwb_temuan','jb.jwb_catatan');
    var $order = array('lg.lingkup_urut' => 'asc');

    function __construct() {
        parent::__construct();
        $this->load->model('LingkupModel', 'lingkup');
    }

    /**
     * Baris induk (data pertanyaan) untuk satu audit + pertanyaan.
     * Dibuat bila belum ada supaya daftar tilik selalu punya tempat berpijak.
     */
    function pastikanInduk($audit_id, $dtform_id) {
        $this->db->where('audit_id', $audit_id);
        $this->db->where('dtform_id', $dtform_id);
        $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        $row = $this->db->get('auditjawab')->row_array();

        if ($row) {
            return $row['jwb_id'];
        }

        $this->db->insert('auditjawab', array(
            'audit_id'  => $audit_id,
            'dtform_id' => $dtform_id,
        ));
        return $this->db->insert_id();
    }

    function getJwbid($audit_id, $dtform_id) {
        return $this->pastikanInduk($audit_id, $dtform_id);
    }

    /** Query dasar daftar tilik: butir lingkup + jawabannya (bila sudah ada). */
    private function _get_datatables_query($search, $ordering, $jwb_id) {
        $this->db->select('lg.lingkup_id, lg.lingkup_urut, lg.lingkup_isi');
        $this->db->select('jb.jwb_id, jb.jwb_hasil, jb.jwb_temuan, jb.jwb_catatan');
        $this->db->from('lingkup lg');
        $this->db->join('auditjawab induk', 'induk.jwb_id = ' . (int) $jwb_id . ' AND induk.dtform_id = lg.dtform_id', 'inner');
        $this->db->join('auditjawab jb', 'jb.audit_id = induk.audit_id AND jb.lingkup_id = lg.lingkup_id', 'left');

        if (!empty($search['value'])) {
            $i = 0;
            $this->db->group_start();
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->like($item, $search['value']);
                } else {
                    $this->db->or_like($item, $search['value']);
                }
                $i++;
            }
            $this->db->group_end();
        }

        if (isset($ordering[0]['column']) && $this->column_order[$ordering[0]['column']]) {
            $this->db->order_by($this->column_order[$ordering[0]['column']], $ordering[0]['dir']);
        } else {
            $this->db->order_by('lg.lingkup_urut', 'asc');
            $this->db->order_by('lg.lingkup_id', 'asc');
        }
    }

    function get_datatables($length, $start, $search, $ordering, $jwb_id) {
        $this->_get_datatables_query($search, $ordering, $jwb_id);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        return $this->db->get()->result();
    }

    function count_filtered($search, $ordering, $jwb_id) {
        $this->_get_datatables_query($search, $ordering, $jwb_id);
        return $this->db->get()->num_rows();
    }

    function count_all($jwb_id) {
        return count($this->butirAudit($jwb_id));
    }

    /** Butir lingkup + jawaban, tanpa paging (dipakai hitungan & ekspor). */
    function butirAudit($jwb_id) {
        $this->_get_datatables_query(array('value' => ''), array(), $jwb_id);
        return $this->db->get()->result_array();
    }

    /** Satu baris jawaban butir (untuk modal edit). */
    function getNilaiByLingkup($jwb_id, $lingkup_id) {
        $induk = $this->induk($jwb_id);
        if (!$induk) {
            return NULL;
        }

        $this->db->where('audit_id', $induk['audit_id']);
        $this->db->where('lingkup_id', $lingkup_id);
        $row = $this->db->get('auditjawab')->row_array();

        if (!$row) {
            // Belum pernah disimpan: kirim kerangka kosong.
            $row = array(
                'jwb_id'      => NULL,
                'jwb_hasil'   => '',
                'jwb_temuan'  => '',
                'jwb_catatan' => '',
            );
        }
        $row['lingkup_id'] = $lingkup_id;
        return $row;
    }

    /**
     * Simpan satu nilai (hasil/temuan/catatan) untuk sebuah butir lingkup.
     * Baris dibuat otomatis bila belum ada.
     */
    function simpanNilai($jwb_id, $lingkup_id, $kolom, $nilai) {
        $diizinkan = array('jwb_hasil', 'jwb_temuan', 'jwb_catatan');
        if (!in_array($kolom, $diizinkan, TRUE)) {
            return FALSE;
        }

        $induk = $this->induk($jwb_id);
        if (!$induk || empty($lingkup_id)) {
            return FALSE;
        }

        $this->db->where('audit_id', $induk['audit_id']);
        $this->db->where('lingkup_id', $lingkup_id);
        $ada = $this->db->get('auditjawab')->row_array();

        if ($ada) {
            $this->db->where('jwb_id', $ada['jwb_id']);
            $this->db->update('auditjawab', array($kolom => $nilai));
        } else {
            $this->db->insert('auditjawab', array(
                'audit_id'   => $induk['audit_id'],
                'dtform_id'  => $induk['dtform_id'],
                'lingkup_id' => $lingkup_id,
                $kolom       => $nilai,
            ));
        }

        return $this->db->affected_rows() >= 0;
    }

    /** Baris induk berdasarkan jwb_id. */
    private function induk($jwb_id) {
        $this->db->where('jwb_id', $jwb_id);
        return $this->db->get('auditjawab')->row_array();
    }

    /* ----------- sisa pemakaian lama (tabel auditjawabdetail) ----------- */

    function getjawabById($dtjwb_id) {
        $this->db->where('dtjwb_id', $dtjwb_id);
        return $this->db->get('auditjawabdetail')->row_array();
    }

    public function add($data) {
        $this->db->insert('auditjawabdetail', $data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('dtjwb_id', $id);
        $this->db->delete('auditjawabdetail');
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("dtjwb_id", $data['dtjwb_id']);
        $this->db->update('auditjawabdetail', $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
