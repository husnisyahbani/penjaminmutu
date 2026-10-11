<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Kotak saring periode - SATU bentuk untuk seluruh halaman.
 *
 * Dipakai kepala panel pada:
 *   - PPM  : Audit > Daftar Audit, Formulir Audit
 *   - Auditor: Formulir (dashboard)
 *   - Auditee: Daftar Audit (dashboard), PTK
 *
 * Ciri kotak saring yang sudah diperbaiki:
 *   1. Pilihan hanya menampilkan TAHUN periode (tanpa tanggal mulai/selesai
 *      dan tanpa tanda "Aktif"), sehingga tidak memakan tempat;
 *   2. Bawaan = periode aktif (kirim PeriodeModel::aktifId() pada argumen
 *      kedua). Bila tidak ada periode aktif, "Semua Periode" yang terpilih;
 *   3. Bila daftar periode kosong (tabel mutu_periode belum disiapkan),
 *      kotak saring tidak ditampilkan sama sekali - bukan pilihan kosong.
 *
 * Pengendali sisi klien cukup membaca $('#filter_periode').val() dan
 * mengirimnya sebagai periode_id (kosong = semua periode).
 *
 * @param  array       $list_periode  hasil PeriodeModel::getAll()
 * @param  int|null    $periode_aktif periode yang dipilih sebagai bawaan
 * @param  string      $id            id elemen <select>
 * @return string      HTML kotak saring, atau '' bila tidak ada periode
 */
if (!function_exists('filter_periode')) {
    function filter_periode($list_periode, $periode_aktif = NULL, $id = 'filter_periode')
    {
        if (empty($list_periode) || !is_array($list_periode)) {
            return '';
        }

        $id = trim((string) $id);
        if ($id === '') {
            $id = 'filter_periode';
        }

        $aktif   = ($periode_aktif === NULL) ? '' : (string) $periode_aktif;
        $pilihan = '';

        foreach ($list_periode as $p) {
            if (!isset($p['periode_id'])) {
                continue;
            }

            /* Hanya tahun yang ditampilkan. Bila tahun belum diisi, pakai
               nomor periode supaya pilihan tetap dapat dikenali. */
            $tahun = isset($p['periode_tahun']) ? trim((string) $p['periode_tahun']) : '';
            if ($tahun === '') {
                $tahun = 'Periode ' . (int) $p['periode_id'];
            }

            $pilihan .= '<option value="' . (int) $p['periode_id'] . '"'
                . (($aktif !== '' && $aktif === (string) $p['periode_id']) ? ' selected' : '')
                . '>' . html_escape($tahun) . '</option>';
        }

        if ($pilihan === '') {
            return '';
        }

        return '<div class="filter-kotak">'
            . '<label for="' . html_escape($id) . '"><i class="icon md-calendar" aria-hidden="true"></i>Periode</label>'
            . '<select class="form-control" id="' . html_escape($id) . '">'
            . '<option value="">Semua Periode</option>'
            . $pilihan
            . '</select>'
            . '</div>';
    }
}
