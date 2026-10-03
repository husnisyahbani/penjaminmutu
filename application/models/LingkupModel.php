<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Butir lingkup audit (tabel lingkup / mutu_lingkup).
 *
 * Sebelumnya satu pertanyaan hanya punya satu kolom teks lingkup
 * (detailform.dtform_lingkup). Sekarang satu pertanyaan boleh punya banyak
 * butir lingkup, dan jawaban audit (auditjawab) berelasi ke butir tersebut
 * melalui kolom lingkup_id.
 *
 * Berkas ini diletakkan di application/models supaya dapat dipakai modul
 * admin, auditor, dan auditee (tanpa salinan ganda).
 */
class LingkupModel extends CI_Model {

    var $tabel        = 'lingkup';
    var $t_detail     = 'detailform';
    var $t_jawab      = 'auditjawab';
    var $t_jawab_det  = 'auditjawabdetail';

    function __construct() {
        parent::__construct();
        $this->load->helper('lingkup');
    }

    /* ==================================================================
       Keadaan tabel
       ================================================================== */

    /** Tabel butir lingkup sudah ada? */
    function siap() {
        return $this->db->table_exists($this->tabel);
    }

    /** Kolom auditjawab.lingkup_id sudah ada? */
    function kolomJawabSiap() {
        return $this->db->field_exists('lingkup_id', $this->t_jawab);
    }

    /** Kolom lama detailform.dtform_lingkup masih ada? */
    function kolomLamaAda() {
        return $this->db->field_exists('dtform_lingkup', $this->t_detail);
    }

    /**
     * Siapkan struktur: tabel lingkup, kolom auditjawab.lingkup_id,
     * penanda auditjawabdetail.lingkup_id, dan kolom lama dibuat boleh kosong
     * supaya pertanyaan baru tetap bisa disimpan.
     *
     * Aman dijalankan berulang kali.
     */
    function install() {
        $this->load->dbforge();

        // 1. Tabel butir lingkup.
        if (!$this->db->table_exists($this->tabel)) {
            $this->dbforge->add_field(array(
                'lingkup_id'     => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE),
                'dtform_id'      => array('type' => 'INT', 'constraint' => 11, 'null' => FALSE),
                'lingkup_urut'   => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'lingkup_isi'    => array('type' => 'TEXT', 'null' => TRUE),
                'lingkup_create' => array('type' => 'DATETIME', 'null' => TRUE),
                'lingkup_update' => array('type' => 'DATETIME', 'null' => TRUE),
            ));
            $this->dbforge->add_key('lingkup_id', TRUE);
            $this->dbforge->add_key('dtform_id');
            $this->dbforge->create_table($this->tabel, TRUE);
        }

        // 2. Relasi jawaban -> butir lingkup.
        if (!$this->db->field_exists('lingkup_id', $this->t_jawab)) {
            $this->dbforge->add_column($this->t_jawab, array(
                'lingkup_id' => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
            ));
        }

        // 3. Penanda pada tabel jawaban lama (agar migrasi tidak jalan dua kali).
        if (!$this->db->field_exists('lingkup_id', $this->t_jawab_det)) {
            $this->dbforge->add_column($this->t_jawab_det, array(
                'lingkup_id' => array('type' => 'INT', 'constraint' => 11, 'null' => TRUE),
            ));
        }

        // 4. Kolom lama tidak lagi wajib diisi (isi dipindah ke tabel lingkup).
        //    Driver sqlite (dipakai untuk uji lokal) tidak mengenal ALTER ... MODIFY.
        $ubah_kolom = !in_array($this->db->dbdriver, array('sqlite3', 'sqlite'), TRUE);
        if ($ubah_kolom && $this->db->field_exists('dtform_lingkup', $this->t_detail)) {
            $this->dbforge->modify_column($this->t_detail, array(
                'dtform_lingkup' => array('type' => 'TEXT', 'null' => TRUE),
            ));
        }

        return TRUE;
    }

    /** Kolom rencana koreksi per butir (auditjawabdetail) sudah ada? */
    function koreksiButirSiap() {
        return $this->db->field_exists('dtjwb_koreksi', $this->t_jawab_det);
    }

    /**
     * Tambah kolom rencana koreksi per butir pada tabel tilik lama
     * (auditjawabdetail.dtjwb_koreksi). Halaman PTK dan delik memakai kolom
     * ini bila ada, sehingga rencana koreksi tersimpan per butir - bukan satu
     * teks untuk satu pertanyaan.
     */
    function installKoreksiButir() {
        if ($this->koreksiButirSiap()) {
            return TRUE;
        }
        /* Driver uji lokal (sqlite) tidak punya Forge untuk ALTER TABLE,
           jadi pernyataannya dijalankan langsung. */
        if (in_array($this->db->dbdriver, array('sqlite', 'sqlite3'), TRUE)) {
            $this->db->query('ALTER TABLE ' . $this->db->protect_identifiers($this->t_jawab_det, TRUE)
                . ' ADD COLUMN dtjwb_koreksi TEXT');
            return $this->koreksiButirSiap();
        }

        $this->load->dbforge();
        $this->dbforge->add_column($this->t_jawab_det, array(
            'dtjwb_koreksi' => array('type' => 'TEXT', 'null' => TRUE),
        ));
        return $this->koreksiButirSiap();
    }

    /** Buang kolom lama setelah migrasi berhasil. */
    function hapusKolomLama() {
        $this->load->dbforge();
        if (!$this->db->field_exists('dtform_lingkup', $this->t_detail)) {
            return TRUE;
        }
        return $this->dbforge->drop_column($this->t_detail, 'dtform_lingkup');
    }

    /* ==================================================================
       Baca
       ================================================================== */

    /** Butir lingkup satu pertanyaan, urut. */
    function butir($dtform_id) {
        if (!$this->siap()) {
            return array();
        }
        $this->db->where('dtform_id', $dtform_id);
        $this->db->order_by('lingkup_urut', 'ASC');
        $this->db->order_by('lingkup_id', 'ASC');
        return $this->db->get($this->tabel)->result_array();
    }

    /** Satu butir lingkup berdasarkan id. */
    function butirSatu($lingkup_id) {
        if (!$this->siap() || empty($lingkup_id)) {
            return NULL;
        }
        $this->db->where('lingkup_id', $lingkup_id);
        return $this->db->get($this->tabel)->row_array();
    }

    /** Jumlah butir (seluruhnya atau per pertanyaan). */
    function hitung($dtform_id = NULL) {
        if (!$this->siap()) {
            return 0;
        }
        if ($dtform_id !== NULL) {
            $this->db->where('dtform_id', $dtform_id);
        }
        return $this->db->count_all_results($this->tabel);
    }

    /** Peta [dtform_id => daftar butir] untuk sekumpulan pertanyaan. */
    function peta($dtform_ids) {
        $hasil = array();
        if (!$this->siap() || empty($dtform_ids)) {
            return $hasil;
        }
        foreach (array_unique($dtform_ids) as $id) {
            $hasil[$id] = array();
        }

        $this->db->select('lingkup_id, dtform_id, lingkup_urut, lingkup_isi');
        $this->db->where_in('dtform_id', array_keys($hasil));
        $this->db->order_by('lingkup_urut', 'ASC');
        $this->db->order_by('lingkup_id', 'ASC');
        foreach ($this->db->get($this->tabel)->result_array() as $row) {
            $hasil[$row['dtform_id']][] = $row;
        }
        return $hasil;
    }

    /** Jumlah butir + kutipan singkat, untuk kolom tabel. */
    function ringkas($dtform_id, $maks = 4) {
        $butir = $this->butir($dtform_id);
        if (!$butir) {
            return '<span class="text-muted">(belum ada butir lingkup)</span>';
        }

        $html = '<span class="badge badge-info">' . count($butir) . ' butir</span><ol class="lingkup-ringkas">';
        $tampil = 0;
        foreach ($butir as $b) {
            if ($tampil >= $maks) {
                break;
            }
            $teks = html_escape(lingkup_bersihkan($b['lingkup_isi']));
            if (hitung_potong($teks) > 110) {
                $teks = mb_substr_potong($teks, 0, 110) . '&hellip;';
            }
            $html .= '<li>' . $teks . '</li>';
            $tampil++;
        }
        if (count($butir) > $tampil) {
            $html .= '<li class="text-muted">+' . (count($butir) - $tampil) . ' butir lainnya&hellip;</li>';
        }
        return $html . '</ol>';
    }

    /** Seluruh butir satu pertanyaan sebagai daftar HTML (untuk halaman baca). */
    function daftarHtml($dtform_id) {
        $butir = $this->butir($dtform_id);
        if (!$butir) {
            return '';
        }
        $item = '';
        foreach ($butir as $b) {
            $item .= '<li>' . html_escape(lingkup_bersihkan($b['lingkup_isi'])) . '</li>';
        }
        return '<ol class="lingkup-daftar">' . $item . '</ol>';
    }

    /* ==================================================================
       Tulis
       ================================================================== */

    /**
     * Simpan butir lingkup sebuah pertanyaan.
     *
     * @param int   $dtform_id
     * @param array $ids  lingkup_id yang dikirim (0 = butir baru), sejajar $isi
     * @param array $isi  teks butir
     * @return array  array('tersimpan' => int, 'dihapus' => int, 'ditahan' => array)
     */
    function simpan($dtform_id, $ids, $isi) {
        $hasil = array('tersimpan' => 0, 'dihapus' => 0, 'ditahan' => array());

        if (!$this->siap()) {
            return $hasil;
        }

        $ids = is_array($ids) ? $ids : array();
        $isi = is_array($isi) ? $isi : array();

        $dipakai = array();
        $urut = 1;

        foreach ($isi as $i => $teks) {
            $teks = trim((string) $teks);
            $id   = isset($ids[$i]) ? (int) $ids[$i] : 0;

            if ($teks === '') {
                if ($id > 0) {
                    $hasil['dihapus'] += $this->hapusButir($id, $hasil['ditahan']) ? 1 : 0;
                }
                continue;
            }

            $data = array(
                'lingkup_isi'  => $teks,
                'lingkup_urut' => $urut,
            );

            if ($id > 0) {
                $data['lingkup_update'] = date('Y-m-d H:i:s');
                $this->db->where('lingkup_id', $id);
                $this->db->update($this->tabel, $data);
                $dipakai[] = $id;
            } else {
                $data['dtform_id']      = $dtform_id;
                $data['lingkup_create'] = date('Y-m-d H:i:s');
                $this->db->insert($this->tabel, $data);
                $dipakai[] = $this->db->insert_id();
            }
            $hasil['tersimpan']++;
            $urut++;
        }

        // Butir yang tidak lagi dikirim dihapus. Butir yang masih dipakai
        // jawaban audit tidak dihapus: urutnya diletakkan di belakang.
        foreach ($this->butir($dtform_id) as $b) {
            if (in_array((int) $b['lingkup_id'], $dipakai, TRUE)) {
                continue;
            }
            if ($this->hapusButir($b['lingkup_id'], $hasil['ditahan'])) {
                $hasil['dihapus']++;
            } else {
                $this->db->where('lingkup_id', $b['lingkup_id']);
                $this->db->update($this->tabel, array('lingkup_urut' => ++$urut));
            }
        }

        return $hasil;
    }

    /**
     * Simpan satu butir (tambah bila $lingkup_id = 0, ubah bila terisi).
     * Dipakai tampilan gaya topik/activity yang mengedit satu baris sekaligus.
     */
    function simpanSatu($dtform_id, $lingkup_id = 0, $isi = '') {
        if (!$this->siap()) {
            return array('status' => FALSE, 'pesan' => 'Tabel lingkup belum disiapkan.');
        }

        $isi = trim((string) $isi);
        if ($isi === '') {
            return array('status' => FALSE, 'pesan' => 'Isi butir tidak boleh kosong.');
        }

        if ((int) $lingkup_id > 0) {
            $butir = $this->butirSatu($lingkup_id);
            if (!$butir || (int) $butir['dtform_id'] !== (int) $dtform_id) {
                return array('status' => FALSE, 'pesan' => 'Butir tidak ditemukan pada pertanyaan ini.');
            }

            $this->db->where('lingkup_id', $lingkup_id);
            $this->db->update($this->tabel, array(
                'lingkup_isi'    => $isi,
                'lingkup_update' => date('Y-m-d H:i:s'),
            ));

            return array(
                'status'     => TRUE,
                'pesan'      => 'Butir diperbarui.',
                'lingkup_id' => (int) $lingkup_id,
                'lingkup_isi' => $isi,
                'lingkup_urut' => (int) $butir['lingkup_urut'],
            );
        }

        $max = $this->db->select_max('lingkup_urut')
            ->where('dtform_id', $dtform_id)
            ->get($this->tabel)->row_array();
        $urut = (int) (isset($max['lingkup_urut']) ? $max['lingkup_urut'] : 0) + 1;

        $this->db->insert($this->tabel, array(
            'dtform_id'      => $dtform_id,
            'lingkup_isi'    => $isi,
            'lingkup_urut'   => $urut,
            'lingkup_create' => date('Y-m-d H:i:s'),
        ));

        return array(
            'status'      => TRUE,
            'pesan'       => 'Butir ditambahkan.',
            'lingkup_id'  => $this->db->insert_id(),
            'lingkup_isi' => $isi,
            'lingkup_urut' => $urut,
        );
    }

    /**
     * Geser satu butir satu langkah (naik/turun) di dalam pertanyaannya.
     * Urutan dinormalkan lebih dahulu supaya nilai urut yang sama tidak
     * membuat perpindahan tidak berefek.
     */
    function pindah($lingkup_id, $arah = 'naik') {
        if (!$this->siap()) {
            return FALSE;
        }

        $butir = $this->butirSatu($lingkup_id);
        if (!$butir) {
            return FALSE;
        }

        $this->normalisasi($butir['dtform_id']);

        $daftar = $this->butir($butir['dtform_id']);
        $ids = array();
        $posisi = NULL;
        foreach ($daftar as $i => $b) {
            $ids[] = (int) $b['lingkup_id'];
            if ((int) $b['lingkup_id'] === (int) $lingkup_id) {
                $posisi = $i;
            }
        }
        if ($posisi === NULL) {
            return FALSE;
        }

        $tujuan = ($arah === 'naik') ? $posisi - 1 : $posisi + 1;
        if ($tujuan < 0 || $tujuan >= count($ids)) {
            return FALSE;
        }

        $this->db->where('lingkup_id', $ids[$posisi]);
        $this->db->update($this->tabel, array('lingkup_urut' => $tujuan + 1));
        $this->db->where('lingkup_id', $ids[$tujuan]);
        $this->db->update($this->tabel, array('lingkup_urut' => $posisi + 1));
        return TRUE;
    }

    /** Rapikan nilai urut menjadi 1..N mengikuti urutan yang tampil. */
    function normalisasi($dtform_id) {
        if (!$this->siap()) {
            return;
        }

        $daftar = $this->butir($dtform_id);
        $urut = 1;
        foreach ($daftar as $b) {
            if ((int) $b['lingkup_urut'] !== $urut) {
                break;
            }
            $urut++;
        }
        if ($urut > count($daftar)) {
            return;   // sudah rapi
        }

        $urut = 1;
        foreach ($daftar as $b) {
            $this->db->where('lingkup_id', $b['lingkup_id']);
            $this->db->update($this->tabel, array('lingkup_urut' => $urut++));
        }
    }

    /**
     * Hapus satu butir. Butir yang sudah dipakai jawaban audit tidak dihapus
     * supaya hasil audit tidak kehilangan relasinya.
     */
    function hapusButir($lingkup_id, &$ditahan = NULL) {
        if ($this->punyaJawaban($lingkup_id)) {
            if (is_array($ditahan)) {
                $ditahan[] = $lingkup_id;
            }
            return FALSE;
        }

        $this->db->where('lingkup_id', $lingkup_id);
        $this->db->delete($this->tabel);
        return $this->db->affected_rows() >= 0;
    }

    /** Apakah butir ini sudah dipakai jawaban audit? */
    function punyaJawaban($lingkup_id) {
        if (!$this->kolomJawabSiap()) {
            return FALSE;
        }
        $this->db->where('lingkup_id', $lingkup_id);
        return $this->db->count_all_results($this->t_jawab) > 0;
    }

    /** Hapus semua butir milik satu pertanyaan (ikut hapus pertanyaan). */
    function hapusByDtform($dtform_id) {
        if (!$this->siap()) {
            return;
        }
        // Jawaban audit yang menunjuk butir ini dilepas relasinya.
        if ($this->kolomJawabSiap()) {
            $this->db->where('dtform_id', $dtform_id);
            $this->db->update($this->t_jawab, array('lingkup_id' => NULL));
        }
        $this->db->where('dtform_id', $dtform_id);
        $this->db->delete($this->tabel);
    }

    /* ==================================================================
       Migrasi data lama
       ================================================================== */

    /**
     * Pecah isi detailform.dtform_lingkup menjadi baris-baris lingkup.
     * Pertanyaan yang sudah punya butir dilewati (aman diulang).
     */
    function parseSemua() {
        $hasil = array('diperiksa' => 0, 'dibuat' => 0, 'butir' => 0, 'dilewati' => 0, 'kosong' => 0);

        if (!$this->siap()) {
            return $hasil;
        }

        $this->db->select('dtform_id, dtform_lingkup');
        $this->db->from($this->t_detail);
        $daftar = $this->db->get()->result_array();

        foreach ($daftar as $d) {
            $hasil['diperiksa']++;

            if ($this->hitung($d['dtform_id']) > 0) {
                $hasil['dilewati']++;
                continue;
            }

            $butir = lingkup_parse(isset($d['dtform_lingkup']) ? $d['dtform_lingkup'] : '');
            if (empty($butir)) {
                $hasil['kosong']++;
                continue;
            }

            $urut = 1;
            $waktu = date('Y-m-d H:i:s');
            foreach ($butir as $teks) {
                $this->db->insert($this->tabel, array(
                    'dtform_id'      => $d['dtform_id'],
                    'lingkup_urut'   => $urut,
                    'lingkup_isi'    => $teks,
                    'lingkup_create' => $waktu,
                ));
                $urut++;
                $hasil['butir']++;
            }
            $hasil['dibuat']++;
        }

        return $hasil;
    }

    /**
     * Tautkan jawaban lama (auditjawabdetail) ke baris jawaban per butir
     * lingkup (auditjawab + kolom lingkup_id) dan tandai lingkup_id pada
     * baris tiliknya, supaya penilaian lama terbaca per butir lingkup.
     *
     * Pencocokan memakai teks pertanyaan butir; baris yang tidak menemukan
     * pasangannya dibiarkan (ditandai dihasil).
     */
    function migrasiJawaban() {
        $hasil = array('diperiksa' => 0, 'dipindah' => 0, 'digabung' => 0, 'tak_cocok' => 0);

        if (!$this->siap() || !$this->kolomJawabSiap()) {
            return $hasil;
        }
        if (!$this->db->field_exists('lingkup_id', $this->t_jawab_det)) {
            return $hasil;
        }

        $this->db->select('*');
        $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        $detail = $this->db->get($this->t_jawab_det)->result_array();

        foreach ($detail as $d) {
            $hasil['diperiksa']++;

            // Baris induk (pertanyaan) memberi audit_id + dtform_id.
            $this->db->where('jwb_id', $d['jwb_id']);
            $induk = $this->db->get($this->t_jawab)->row_array();
            if (!$induk) {
                $hasil['tak_cocok']++;
                continue;
            }

            $butir = $this->cocokkanButir($induk['dtform_id'], $d['dtjwb_pertanyaan']);
            if (!$butir) {
                $hasil['tak_cocok']++;
                continue;
            }

            // Sudah ada baris jawaban untuk butir ini?
            $this->db->where('audit_id', $induk['audit_id']);
            $this->db->where('lingkup_id', $butir['lingkup_id']);
            $ada = $this->db->get($this->t_jawab)->row_array();

            /* Penilaian (hasil/temuan/catatan) tetap dibaca dari baris
               mutu_auditjawabdetail, jadi yang dipindahkan hanya penanda
               lingkup_id-nya. Kolom penilaian pada mutu_auditjawab
               (jwb_hasil / jwb_temuan / jwb_catatan) sudah dihapus. */
            if ($ada) {
                $hasil['digabung']++;
            } else {
                $this->db->insert($this->t_jawab, array(
                    'audit_id'   => $induk['audit_id'],
                    'dtform_id'  => $induk['dtform_id'],
                    'lingkup_id' => $butir['lingkup_id'],
                ));
                $hasil['dipindah']++;
            }

            // Tandai baris lama agar tidak diproses dua kali.
            $this->db->where('dtjwb_id', $d['dtjwb_id']);
            $this->db->update($this->t_jawab_det, array('lingkup_id' => $butir['lingkup_id']));
        }

        return $hasil;
    }

    /** Cari butir lingkup yang teksnya sama dengan pertanyaan tilik lama. */
    function cocokkanButir($dtform_id, $teks) {
        $kunci = lingkup_normal($teks);
        if ($kunci === '') {
            return NULL;
        }

        foreach ($this->butir($dtform_id) as $b) {
            $banding = lingkup_normal($b['lingkup_isi']);
            if ($banding === '') {
                continue;
            }
            if ($banding === $kunci) {
                return $b;
            }
            // Teks lama kadang terpotong / ditambah keterangan.
            if (hitung_potong($banding) > 25 && (strpos($banding, $kunci) !== FALSE || strpos($kunci, $banding) !== FALSE)) {
                return $b;
            }
        }
        return NULL;
    }

    /** Ringkasan keadaan untuk halaman migrasi. */
    function status() {
        $s = array(
            'tabel_lingkup'   => $this->siap(),
            'kolom_jawab'     => $this->kolomJawabSiap(),
            'kolom_lama'      => $this->kolomLamaAda(),
            'koreksi_butir'   => $this->koreksiButirSiap(),
            'lampiran_jawaban' => $this->db->table_exists('lampiran')
                && $this->db->field_exists('jwb_id', 'lampiran'),
            'jml_dtform'      => 0,
            'jml_butir'       => 0,
            'jml_jawaban_butir' => 0,
            'jml_jawaban_lama'  => 0,
            'jml_tilik_lama'    => 0,
            'jml_tilik_belum'   => 0,
        );

        $s['jml_dtform'] = $this->db->count_all_results($this->t_detail);

        if ($s['tabel_lingkup']) {
            $s['jml_butir'] = $this->hitung();
        }
        if ($s['kolom_jawab']) {
            $this->db->where('lingkup_id IS NOT NULL', NULL, FALSE);
            $s['jml_jawaban_butir'] = $this->db->count_all_results($this->t_jawab);
        }
        if ($s['kolom_jawab']) {
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
            $s['jml_jawaban_lama'] = $this->db->count_all_results($this->t_jawab);
        } else {
            // Kolom belum dipasang: semua baris masih bentuk lama.
            $s['jml_jawaban_lama'] = $this->db->count_all_results($this->t_jawab);
        }

        if ($this->db->table_exists($this->t_jawab_det) && $this->db->field_exists('lingkup_id', $this->t_jawab_det)) {
            $s['jml_tilik_lama'] = $this->db->count_all_results($this->t_jawab_det);
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
            $s['jml_tilik_belum'] = $this->db->count_all_results($this->t_jawab_det);
        }

        return $s;
    }
}
