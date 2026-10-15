<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Peta audit - SATU susunan data untuk halaman detail audit.
 *
 * Isi: pertanyaan (topik) formulir, butir lingkup pada tiap pertanyaan,
 * jawaban auditee per butir, lampiran, serta catatan "Kelebihan" dan
 * "Peluang untuk peningkatan" (tabel mutu_lingkup_nilai).
 *
 * Dipakai bersama oleh:
 *   - auditor/daftaraudit/detail  (lewat AuditjawabModel::petaLingkup)
 *   - admin/daftaraudit/detail    (langsung dari controller)
 *
 * Kueri ini dahulu berada di dalam modules/auditor/models/AuditjawabModel.php.
 * Dipindahkan ke sini supaya halaman PPM menampilkan susunan yang sama
 * persis tanpa menyalin kode.
 *
 * Catatan: penilaian (hasil/temuan/catatan/koreksi) TIDAK dihasilkan di sini;
 * nilainya diisi pada halaman Daftar Tilik dan terbaca pada ekspor Excel.
 */
class PetaauditModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /** Tabel mutu_lingkup sudah tersedia? */
    public function siap() {
        return $this->db->table_exists('lingkup');
    }

    /**
     * Susunan lengkap satu audit: pertanyaan -> butir lingkup -> jawaban
     * auditee, lampiran, dan catatan kelebihan/peluang.
     *
     * @param  int   $audit_id
     * @return array
     */
    function peta($audit_id) {
        $this->load->helper('lingkup');
        $this->load->model('LingkupModel', 'lingkupmodel');
        $this->load->model('LampiranModel', 'lampiranmodel');

        $audit = $this->db->select('audit_id, form_id')
            ->where('audit_id', $audit_id)
            ->get('audit')->row_array();
        if (!$audit) {
            return array();
        }

        /* Pertanyaan (topik) formulir audit ini. */
        $this->db->where('form_id', $audit['form_id']);
        if ($this->db->field_exists('dtform_urut', 'detailform')) {
            $this->db->order_by('dtform_urut', 'ASC');
        }
        $this->db->order_by('dtform_id', 'ASC');
        $topik = $this->db->get('detailform')->result_array();
        if (!$topik) {
            return array();
        }

        $dtform_ids = array();
        foreach ($topik as $t) {
            $dtform_ids[] = (int) $t['dtform_id'];
        }

        /* Butir lingkup per pertanyaan (tabel mutu_lingkup). */
        $peta_butir = $this->siap()
            ? $this->lingkupmodel->peta($dtform_ids)
            : array();

        /* Jawaban per butir (auditjawab.lingkup_id terisi). */
        $jawab_butir = array();
        if ($this->_adaLingkup()) {
            $this->db->select('jwb_id, dtform_id, lingkup_id, jwb_jawaban');
                $this->db->where('audit_id', $audit_id);
            $this->db->where('lingkup_id IS NOT NULL', NULL, FALSE);
            foreach ($this->db->get('auditjawab')->result_array() as $j) {
                $jawab_butir[(int) $j['lingkup_id']] = $j;
            }
        }

        /* Jawaban tingkat pertanyaan (cadangan pertanyaan tanpa butir). */
        $induk = array();
        $this->db->select('jwb_id, dtform_id, jwb_jawaban, jwb_tujuan');
        $this->db->where('audit_id', $audit_id);
        if ($this->_adaLingkup()) {
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        }
        foreach ($this->db->get('auditjawab')->result_array() as $j) {
            $induk[(int) $j['dtform_id']] = $j;
        }

        /* Penilaian auditor pada struktur lama (auditjawabdetail). */
        $lama = array();
        /* Kolom lama mutu_auditjawab.jwb_koreksi sudah dihapus: rencana
           koreksi hanya ada pada mutu_auditjawabdetail.dtjwb_koreksi. */

        $this->db->select('d.dtjwb_id, d.dtjwb_referensi, d.dtjwb_pertanyaan');
        $this->db->select('d.dtjwb_hasil, d.dtjwb_temuan, d.dtjwb_catatan');
        $this->db->select('j.jwb_id, j.dtform_id');
        if ($this->db->field_exists('dtjwb_koreksi', 'auditjawabdetail')) {
            $this->db->select('d.dtjwb_koreksi AS jwb_koreksi', FALSE);
        }
        if ($this->db->field_exists('lingkup_id', 'auditjawabdetail')) {
            $this->db->select('d.lingkup_id AS dtjwb_lingkup_id', FALSE);
        }
        $this->db->from('auditjawabdetail d');
        $this->db->join('auditjawab j', 'j.jwb_id = d.jwb_id', 'inner');
        $this->db->where('j.audit_id', $audit_id);
        $this->db->order_by('j.dtform_id', 'ASC');
        $this->db->order_by('d.dtjwb_id', 'ASC');
        foreach ($this->db->get()->result_array() as $b) {
            if (!isset($b['dtjwb_lingkup_id'])) {
                $b['dtjwb_lingkup_id'] = NULL;
            }
            $lama[(int) $b['dtform_id']][] = $b;
        }

        /* Catatan auditor per butir lingkup: kelebihan & peluang untuk
           peningkatan (tabel mutu_lingkup_nilai). Bila tabelnya belum ada,
           kunci kelebihan/peluang dikosongkan. */
        $this->load->model('LingkupnilaiModel', 'lingkupnilaimodel');
        $nilai_lingkup = $this->lingkupnilaimodel->siap()
            ? $this->lingkupnilaimodel->peta($audit_id)
            : array();

        /* Lampiran: per butir (lingkup_id), per baris tilik lama
           (dtjwb_id), dan per jawaban pertanyaan (jwb_id). */
        $lampiran_butir = array();
        $lampiran_tilik = array();
        $lampiran_jawab = array();
        if ($this->lampiranmodel->siap()) {
            if ($this->db->field_exists('lingkup_id', 'lampiran')) {
                $lampiran_butir = $this->lampiranmodel->peta($audit_id, 'lingkup_id');
            }
            if ($this->db->field_exists('dtjwb_id', 'lampiran')) {
                $lampiran_tilik = $this->lampiranmodel->peta($audit_id, 'dtjwb_id');
            }
            $lampiran_jawab = $this->lampiranmodel->petaJawaban($audit_id);
        }

        foreach ($topik as $i => $t) {
            $tid              = (int) $t['dtform_id'];
            $jawab_pertanyaan = isset($induk[$tid]) ? $induk[$tid] : NULL;

            /* Pisahkan baris tilik lama: yang sudah punya lingkup_id dan
               yang masih harus dicocokkan berdasar teks. */
            $sisa        = isset($lama[$tid]) ? $lama[$tid] : array();
            $berdasar_id = array();
            foreach ($sisa as $kunci => $b) {
                $lid = (int) $b['dtjwb_lingkup_id'];
                if ($lid > 0) {
                    $berdasar_id[$lid][] = $b;
                    unset($sisa[$kunci]);
                }
            }

            /* Butir lingkup = pertanyaan yang dijawab auditee. */
            $butir = array();
            if (isset($peta_butir[$tid])) {
                foreach ($peta_butir[$tid] as $b) {
                    $pen = $this->_penilaianLama((int) $b['lingkup_id'], $b['lingkup_isi'], $berdasar_id, $sisa);
                    $butir[] = $this->_susunButir($tid, $b, $jawab_butir, $pen,
                        $lampiran_butir, $lampiran_tilik, $nilai_lingkup);
                }
            }

            /* Baris tilik lama (mutu_auditjawabdetail) yang tidak berpasangan
               dengan butir lingkup mana pun TIDAK ditampilkan di halaman ini:
               daftar pertanyaannya murni mutu_lingkup. Nilai penilaian dari
               baris tilik tetap terbaca lewat _penilaianLama() di atas, dan
               daftar tilik lengkapnya ada pada halaman Daftar Tilik. */

            $jml_butir = 0;
            $jml_dijawab = 0;
            $jml_lampiran = 0;

            foreach ($butir as $b) {
                if ((int) $b['lingkup_id'] > 0) {
                    $jml_butir++;
                    if (!empty($b['sudah_dijawab'])) {
                        $jml_dijawab++;
                    }
                }
                $jml_lampiran += count($b['lampiran']);
            }

            $punya_lingkup = ($jml_butir > 0);
            $jawaban_topik = $jawab_pertanyaan ? trim((string) $jawab_pertanyaan['jwb_jawaban']) : '';

            $lampiran_topik = ($jawab_pertanyaan && isset($lampiran_jawab[(int) $jawab_pertanyaan['jwb_id']]))
                ? $lampiran_jawab[(int) $jawab_pertanyaan['jwb_id']]
                : array();
            $jml_lampiran += count($lampiran_topik);

            $topik[$i]['butir']          = $butir;
            $topik[$i]['jwb']            = $jawab_pertanyaan;
            $topik[$i]['lampiran']       = $lampiran_topik;
            $topik[$i]['punya_lingkup']  = $punya_lingkup;
            $topik[$i]['sudah_dijawab']  = $punya_lingkup
                ? ($jml_butir === $jml_dijawab)
                : ($jawaban_topik !== '');
            $topik[$i]['teks']           = lingkup_bersihkan($t['dtform_pertanyaan']);
            $topik[$i]['jml_butir']      = $jml_butir;
            $topik[$i]['jml_dijawab']    = $jml_dijawab;
            $topik[$i]['jml_belum']      = $jml_butir - $jml_dijawab;
            $topik[$i]['jml_wajib']      = $punya_lingkup ? $jml_butir : 1;
            $topik[$i]['jml_lampiran']   = $jml_lampiran;
        }

        return $topik;
    }

    public function lingkupMilikAudit($audit_id, $lingkup_id) {
        $audit_id   = (int) $audit_id;
        $lingkup_id = (int) $lingkup_id;
        if ($audit_id < 1 || $lingkup_id < 1) {
            return FALSE;
        }
        if (!$this->db->table_exists('lingkup')) {
            return FALSE;
        }

        $this->db->select('au.audit_id');
        $this->db->from('audit au');
        $this->db->join('detailform dt', 'dt.form_id = au.form_id', 'inner');
        $this->db->join('lingkup lg', 'lg.dtform_id = dt.dtform_id', 'inner');
        $this->db->where('au.audit_id', $audit_id);
        $this->db->where('lg.lingkup_id', $lingkup_id);

        return (bool) $this->db->get()->row_array();
    }

    private function _adaLingkup() {
        return $this->db->field_exists('lingkup_id', 'auditjawab');
    }

    private function _penilaianLama($lingkup_id, $teks, &$berdasar_id, &$sisa) {
        if ($lingkup_id > 0 && !empty($berdasar_id[$lingkup_id])) {
            return array_shift($berdasar_id[$lingkup_id]);
        }

        $kunci = lingkup_normal($teks);
        if ($kunci === '') {
            return NULL;
        }

        // Cocok persis lebih dahulu, baru kemiripan sebagian.
        foreach ($sisa as $idx => $row) {
            $banding = lingkup_normal($row['dtjwb_pertanyaan']);
            if ($banding !== '' && $banding === $kunci) {
                $hasil = $row;
                unset($sisa[$idx]);
                return $hasil;
            }
        }
        foreach ($sisa as $idx => $row) {
            $banding = lingkup_normal($row['dtjwb_pertanyaan']);
            if ($banding === '' || hitung_potong($banding) <= 25) {
                continue;
            }
            if (strpos($banding, $kunci) !== FALSE || strpos($kunci, $banding) !== FALSE) {
                $hasil = $row;
                unset($sisa[$idx]);
                return $hasil;
            }
        }

        return NULL;
    }

    private function _susunButir($dtform_id, $b, $jawab_butir, $pen, $lampiran_butir,
                                $lampiran_tilik, $nilai_lingkup = array()) {
        $lid = (int) $b['lingkup_id'];
        $jb  = isset($jawab_butir[$lid]) ? $jawab_butir[$lid] : NULL;

        $jawaban = $jb ? trim((string) $jb['jwb_jawaban']) : '';

        $item = array(
            'lingkup_id'      => $lid,
            'lingkup_isi'     => $b['lingkup_isi'],
            'lingkup_teks'    => lingkup_bersihkan($b['lingkup_isi']),
            'lingkup_urut'    => (int) $b['lingkup_urut'],
            'dtform_id'       => (int) $dtform_id,
            'jwb_id'          => $jb ? (int) $jb['jwb_id'] : 0,
            'jwb_jawaban'     => $jawaban,
            'sudah_dijawab'   => ($jawaban !== ''),
            /* Catatan auditor per butir lingkup (kelebihan & peluang
               peningkatan) - kosong bila belum diisi. */
            'kelebihan'       => isset($nilai_lingkup[$lid]['kelebihan']) ? $nilai_lingkup[$lid]['kelebihan'] : '',
            'peluang'         => isset($nilai_lingkup[$lid]['peluang']) ? $nilai_lingkup[$lid]['peluang'] : '',
            /* dtjwb_id tetap diisi: dipakai untuk memetakan lampiran lama
               yang menempel pada baris tilik (mutu_auditjawabdetail). */
            'dtjwb_id'        => $pen ? (int) $pen['dtjwb_id'] : 0,
            'bisa_dijawab'    => TRUE,
            'lampiran'        => array(),
        );

        $kumpul = array();
        if ($lid > 0 && isset($lampiran_butir[$lid])) {
            foreach ($lampiran_butir[$lid] as $l) {
                $kumpul[(int) $l['lampiran_id']] = $l;
            }
        }
        if ($item['dtjwb_id'] > 0 && isset($lampiran_tilik[$item['dtjwb_id']])) {
            foreach ($lampiran_tilik[$item['dtjwb_id']] as $l) {
                $kumpul[(int) $l['lampiran_id']] = $l;
            }
        }
        $item['lampiran'] = array_values($kumpul);

        return $item;
    }
}

