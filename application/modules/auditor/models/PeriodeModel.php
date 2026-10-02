<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Periode Audit - bagian yang dipakai modul ini.
 *
 * Tabel mutu_periode dan kolom mutu_audit.periode_id dibuat dari menu
 * admin: Audit > Periode (atau impor database/periode_audit.sql).
 * Bila belum disiapkan, aktifId() mengembalikan NULL sehingga audit baru
 * tetap dapat dibuat tanpa periode.
 */
class PeriodeModel extends CI_Model {

    var $tabel     = 'mutu_periode';
    var $t_audit   = 'mutu_audit';
    var $kol_audit = 'periode_id';
    var $t_formulir  = 'mutu_formulir';
    var $kol_formulir = 'periode_id';

    function __construct() {
        parent::__construct();
    }

    /** Apakah tabel periode + kolom relasi sudah siap. */
    function siap() {
        return $this->db->table_exists($this->tabel)
            && $this->db->field_exists($this->kol_audit, $this->t_audit);
    }

    /** Kolom relasi periode pada mutu_formulir sudah ada? */
    function formulirSiap() {
        return $this->db->table_exists($this->t_formulir)
            && $this->db->field_exists($this->kol_formulir, $this->t_formulir);
    }

    /** ID periode aktif, atau NULL bila tabel belum ada / tidak ada yang aktif. */
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

    /** Seluruh periode, terbaru lebih dahulu (dipakai filter daftar formulir). */
    function getAll() {
        if (!$this->db->table_exists($this->tabel)) {
            return array();
        }
        return $this->db->order_by('periode_tahun', 'DESC')
                        ->order_by('periode_id', 'DESC')
                        ->get($this->tabel)->result_array();
    }
}
