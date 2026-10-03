<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AuditjawabModel extends CI_Model {

    /** Kolom auditjawab.lingkup_id sudah ada? (struktur lingkup) */
    private function _adaLingkup() {
        return $this->db->field_exists('lingkup_id', 'auditjawab');
    }

    /** Potongan SQL "AND lingkup_id IS NULL" bila kolomnya ada. */
    private function _lingkupNull() {
        return $this->_adaLingkup() ? ' AND lingkup_id IS NULL' : '';
    }


    function __construct() {
        parent::__construct();
    }

    /* Kolom penilaian (jwb_hasil/jwb_temuan/jwb_catatan) sudah dihapus
       dari mutu_auditjawab. */
    var $column_search = array('dtform_pertanyaan','jwb_jawaban');
    /* Lingkup kini berupa daftar butir (ditampilkan dari tabel lingkup). */
    var $column_order = array(null,'dtform_pertanyaan','dtform_pertanyaan','jwb_jawaban');
    var $order = array('audit_id' => 'asc');

    private function _get_datatables_query($search, $ordering) {
        $i = 0;

        foreach ($this->column_search as $item) { // looping awal
            if ($search['value']) { // jika datatable mengirimkan pencarian dengan metode POST
                if ($i === 0) { // looping awal
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

        if (isset($ordering)) {
            $this->db->order_by($this->column_order[$ordering[0]['column']], $ordering[0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($length, $start, $search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $this->db->select("audit_id");
        $this->db->select("audit_status");
        $this->db->select("dt.dtform_id as dtform_id");
        $this->db->select("dt.dtform_pertanyaan as dtform_pertanyaan");
        $this->db->select("(SELECT jwb_tujuan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id" . $this->_lingkupNull() . ") as jwb_tujuan");
        $this->db->select("(SELECT jwb_jawaban from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id" . $this->_lingkupNull() . ") as jwb_jawaban");
        $this->db->from('audit au');
        $this->db->join('detailform dt', 'dt.form_id = au.form_id', 'left');
        $this->db->where('au.audit_id',$id);
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('au.auditor_id',$users_id);
        }
        $query = $this->db->get();
        return $this->_lampirkanLingkup($query->result());
    }

    /**
     * Lampirkan butir lingkup (tabel lingkup) ke tiap baris sebagai
     * dtform_lingkup berisi daftar HTML, supaya tampilan lama tetap jalan.
     */
    private function _lampirkanLingkup($rows) {
        if (empty($rows)) {
            return $rows;
        }
        $this->load->model('LingkupModel', 'lingkup');

        $ids = array();
        foreach ($rows as $r) {
            if (isset($r->dtform_id)) {
                $ids[] = $r->dtform_id;
            }
        }
        $peta = $this->lingkup->peta($ids);

        foreach ($rows as $r) {
            $r->dtform_lingkup = isset($peta[$r->dtform_id])
                ? $this->_htmlLingkup($peta[$r->dtform_id])
                : '';
        }
        return $rows;
    }

    private function _htmlLingkup($butir) {
        if (empty($butir)) {
            return '';
        }
        $item = '';
        foreach ($butir as $b) {
            $item .= '<li>' . html_escape(lingkup_bersihkan($b['lingkup_isi'])) . '</li>';
        }
        return '<ol class="lingkup-daftar">' . $item . '</ol>';
    }

    function count_filtered($search, $ordering,$id) {
        $this->_get_datatables_query($search, $ordering);
        $this->db->select("audit_id");
        $this->db->select("audit_status");
        $this->db->select("dt.dtform_id as dtform_id");
        $this->db->select("dt.dtform_pertanyaan as dtform_pertanyaan");
        $this->db->select("(SELECT jwb_jawaban from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id" . $this->_lingkupNull() . ") as jwb_jawaban");
        $this->db->from('audit au');
        $this->db->join('detailform dt', 'dt.form_id = au.form_id', 'left');
        $this->db->where('au.audit_id',$id);
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('au.auditor_id',$users_id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id) {
        $this->db->from('audit');
        $this->db->where('audit.audit_id',$id);
        $users_id = $this->session->userdata('users_id');
        if(isset($users_id)){
            $this->db->where('audit.auditor_id',$users_id);
        }
        return $this->db->count_all_results();
    }

    public function add($data) {
        // Baris tingkat pertanyaan: lingkup_id selalu NULL.
        if ($this->_adaLingkup() && !array_key_exists('lingkup_id', $data)) {
            $data['lingkup_id'] = NULL;
        }
        $this->db->insert('auditjawab',$data);
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function hapus($id) {
        $this->db->where('jwb_id',$id);
        $this->db->from('auditjawab');
        $this->db->delete();
        return($this->db->affected_rows() != 1) ? false : true;
    }

    public function getAuditJawab($audit_id,$dtform_id){
        $this->db->join('detailform', 'detailform.dtform_id = auditjawab.dtform_id', 'left');
        $this->db->where($this->db->dbprefix('auditjawab').'.audit_id', $audit_id);
        $this->db->where($this->db->dbprefix('auditjawab').'.dtform_id', $dtform_id);
        if ($this->_adaLingkup()) {
            $this->db->where($this->db->dbprefix('auditjawab').'.lingkup_id IS NULL', NULL, FALSE);
        }
        $query = $this->db->get('auditjawab');
        $jawab = $query->row_array();

        // Butir lingkup pertanyaan ini (relasi baru: auditjawab.lingkup_id).
        $this->load->model('LingkupModel', 'lingkup');
        $jawab['lingkup'] = $this->lingkup->butir($dtform_id);
        $jawab['dtform_lingkup'] = $this->_htmlLingkup($jawab['lingkup']);

        return $jawab;
    }

    public function getAuditJawabFix($audit_id,$dtform_id){
        $this->db->where($this->db->dbprefix('auditjawab').'.audit_id', $audit_id);
        $this->db->where($this->db->dbprefix('auditjawab').'.dtform_id', $dtform_id);
        if ($this->_adaLingkup()) {
            $this->db->where($this->db->dbprefix('auditjawab').'.lingkup_id IS NULL', NULL, FALSE);
        }
        $query = $this->db->get('auditjawab');
        return $query->row_array();
    }

     public function jawab($data) {
        $this->db->trans_start();
        $this->db->where("audit_id",$data['audit_id']);
        $this->db->where("dtform_id",$data['dtform_id']);
        if ($this->_adaLingkup()) {
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        }
        $this->db->update('auditjawab',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function is_exist($data) {
        
        $this->db->where("audit_id",$data['audit_id']);
        $this->db->where("dtform_id",$data['dtform_id']);
        if ($this->_adaLingkup()) {
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        }
        $query = $this->db->get('auditjawab');
        if ($query->num_rows() > 0) {
            return true; // data ada
        } else {
            return false; // data tidak ada
        }
    }

    

    public function edit($data) {
        $this->db->trans_start();
        $this->db->where("jwb_id",$data['jwb_id']);
        $this->db->update('auditjawab',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }


    /**
     * Penilaian auditor & rencana koreksi per butir lingkup pada satu
     * pertanyaan, dibaca dari mutu_auditjawabdetail.
     *
     * Kolom penilaian pada mutu_auditjawab (jwb_hasil, jwb_temuan,
     * jwb_catatan, jwb_koreksi) sudah dihapus, jadi satu-satunya sumber
     * nilai adalah baris tilik (mutu_auditjawabdetail) yang
     * lingkup_id-nya terisi.
     *
     * @param  int $audit_id
     * @param  int $dtform_id
     * @return array lingkup_id => array(dtjwb_hasil, dtjwb_temuan,
     *               dtjwb_catatan, dtjwb_koreksi, dtjwb_referensi)
     */
    private function _nilaiPerLingkup($audit_id, $dtform_id) {
        $nilai = array();
        if (!$this->db->field_exists('lingkup_id', 'auditjawabdetail')) {
            return $nilai;
        }

        $this->db->select('d.dtjwb_id, d.lingkup_id, d.dtjwb_referensi');
        $this->db->select('d.dtjwb_hasil, d.dtjwb_temuan, d.dtjwb_catatan');
        if ($this->db->field_exists('dtjwb_koreksi', 'auditjawabdetail')) {
            $this->db->select('d.dtjwb_koreksi');
        }
        $this->db->from('auditjawabdetail d');
        $this->db->join('auditjawab j', 'j.jwb_id = d.jwb_id', 'inner');
        $this->db->where('j.audit_id', (int) $audit_id);
        $this->db->where('j.dtform_id', (int) $dtform_id);
        $this->db->where('d.lingkup_id IS NOT NULL', NULL, FALSE);
        $this->db->order_by('d.dtjwb_id', 'ASC');
        foreach ($this->db->get()->result_array() as $d) {
            $lid = (int) $d['lingkup_id'];
            if ($lid > 0 && !isset($nilai[$lid])) {
                $nilai[$lid] = $d;
            }
        }

        return $nilai;
    }

    /**
     * Baris daftar tilik satu pertanyaan, digabung dari struktur baru
     * (butir lingkup + auditjawab) dan sisa data lama yang belum terpetakan.
     * Kunci keluaran mengikuti nama lama (dtjwb_*) agar tampilan/ekspor tetap jalan.
     */
    public function barisTilik($audit_id, $dtform_id) {
        /* Bila tabel lingkup (struktur lama) belum ada, butir tilik dibaca
           dari mutu_auditjawabdetail dengan kunci lama (dtjwb_*). */
        if (!$this->db->table_exists('lingkup')) {
            /* Rencana koreksi tersimpan pada mutu_auditjawabdetail.dtjwb_koreksi;
               kolom lama mutu_auditjawab.jwb_koreksi sudah dihapus. */
            $this->db->select('d.dtjwb_id, d.dtjwb_referensi, d.dtjwb_pertanyaan');
            $this->db->select('d.dtjwb_hasil, d.dtjwb_temuan, d.dtjwb_catatan');
            if ($this->db->field_exists('dtjwb_koreksi', 'auditjawabdetail')) {
                $this->db->select('d.dtjwb_koreksi');
            }
            $this->db->from('auditjawabdetail d');
            $this->db->join('auditjawab j', 'j.jwb_id = d.jwb_id', 'inner');
            $this->db->where('j.audit_id', $audit_id);
            $this->db->where('j.dtform_id', $dtform_id);
            $this->db->order_by('d.dtjwb_id', 'asc');

            $baris = array();
            foreach ($this->db->get()->result_array() as $b) {
                if (!isset($b['dtjwb_koreksi'])) {
                    $b['dtjwb_koreksi'] = '';
                }
                $b['lingkup_id'] = $b['dtjwb_id'];
                $b['baris']      = 'butir';
                $baris[]         = $b;
            }
            return $baris;
        }

        $this->load->model('LingkupModel', 'lingkup');

        $baris = array();

        // a) butir lingkup struktur baru
        /* Penilaian (hasil/temuan/catatan) serta rencana koreksi tersimpan
           pada mutu_auditjawabdetail; kolom penilaian pada mutu_auditjawab
           (jwb_hasil/jwb_temuan/jwb_catatan/jwb_koreksi) sudah dihapus. */
        $nilai = $this->_nilaiPerLingkup($audit_id, $dtform_id);

        $this->db->select('lg.lingkup_id, lg.lingkup_isi');
        $this->db->from('lingkup lg');
        $this->db->where('lg.dtform_id', $dtform_id);
        $this->db->order_by('lg.lingkup_urut', 'asc');
        $this->db->order_by('lg.lingkup_id', 'asc');
        foreach ($this->db->get()->result_array() as $b) {
            $lid = (int) $b['lingkup_id'];
            $n   = isset($nilai[$lid]) ? $nilai[$lid] : array();
            $baris[] = array(
                'dtjwb_id'         => $b['lingkup_id'],
                'dtjwb_referensi'  => isset($n['dtjwb_referensi']) ? $n['dtjwb_referensi'] : '',
                'dtjwb_pertanyaan' => $b['lingkup_isi'],
                'dtjwb_hasil'      => isset($n['dtjwb_hasil']) ? $n['dtjwb_hasil'] : '',
                'dtjwb_temuan'     => isset($n['dtjwb_temuan']) ? $n['dtjwb_temuan'] : '',
                'dtjwb_catatan'    => isset($n['dtjwb_catatan']) ? $n['dtjwb_catatan'] : '',
                'dtjwb_koreksi'    => isset($n['dtjwb_koreksi']) ? $n['dtjwb_koreksi'] : '',
                'lingkup_id'       => $b['lingkup_id'],
                'baris'            => 'butir',
            );
        }

        // b) sisa baris lama yang belum dipetakan ke butir mana pun
        $jwb = $this->getAuditJawabFix($audit_id, $dtform_id);
        if (!empty($jwb['jwb_id'])) {
            $this->db->where('jwb_id', $jwb['jwb_id']);
            if ($this->_adaLingkup()) {
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        }
            foreach ($this->db->get('auditjawabdetail')->result_array() as $d) {
                /* Baris lama boleh jadi tidak punya kolom koreksi
                   (mutu_auditjawabdetail.dtjwb_koreksi) dan kolom referensi.
                   Lengkapi kuncinya agar bentuknya sama dengan baris butir,
                   sehingga pemakai tidak menemui "undefined index". */
                $d['dtjwb_referensi'] = isset($d['dtjwb_referensi']) ? $d['dtjwb_referensi'] : '';
                $d['dtjwb_koreksi']   = isset($d['dtjwb_koreksi']) ? $d['dtjwb_koreksi'] : '';
                $d['lingkup_id']      = isset($d['lingkup_id']) ? $d['lingkup_id'] : NULL;
                $d['baris'] = 'lama';
                $baris[] = $d;
            }
        }

        return $baris;
    }

    /**
     * Susunan topik & activity satu audit (gaya halaman kursus).
     *
     * Topik    = pertanyaan formulir (urut dtform_urut bila kolomnya ada)
     * Activity = butir lingkup pertanyaan tersebut, dilengkapi jawaban yang
     *            sudah tersimpan (hasil/temuan/catatan/koreksi).
     *
     * @return array daftar topik; tiap topik berisi 'butir' dan 'jwb'.
     */
    /**
     * Peta pertanyaan (topik) + butir tilik beserta penilaian auditor dan
     * jawaban auditee.
     *
     * - Butir tilik dibaca dari `auditjawabdetail`: hanya AUDITOR yang mengisi
     *   hasil/temuan/catatan. Auditee tidak menjawab butir; auditee hanya
     *   menambahkan rencana koreksi (dtjwb_koreksi) setelah audit selesai.
     * - Jawaban auditee ada pada baris PERTANYAAN (`auditjawab.jwb_jawaban`),
     *   begitu pula lampirannya (`mutu_lampiran.jwb_id`).
     */
    function petaTilik($audit_id) {
        /* Pembantu teks butir (lingkup_bersihkan). */
        $this->load->helper('lingkup');

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

        /* Jawaban tingkat pertanyaan (jawaban auditee + tujuan). */
        $induk = array();
        $this->db->select('jwb_id, dtform_id, jwb_jawaban, jwb_tujuan');
        $this->db->where('audit_id', $audit_id);
        if ($this->db->field_exists('lingkup_id', 'auditjawab')) {
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        }
        foreach ($this->db->get('auditjawab')->result_array() as $j) {
            $induk[(int) $j['dtform_id']] = $j;
        }

        /* Butir tilik + penilaian auditor (read-only bagi auditee). */
        /* Kolom lama mutu_auditjawab.jwb_koreksi sudah dihapus: rencana
           koreksi hanya ada pada mutu_auditjawabdetail.dtjwb_koreksi. */

        $this->db->select('d.dtjwb_id, d.dtjwb_referensi, d.dtjwb_pertanyaan');
        $this->db->select('d.dtjwb_hasil, d.dtjwb_temuan, d.dtjwb_catatan');
        $this->db->select('j.jwb_id, j.dtform_id');
        if ($this->db->field_exists('dtjwb_koreksi', 'auditjawabdetail')) {
            $this->db->select('d.dtjwb_koreksi AS jwb_koreksi', FALSE);
        }
        $this->db->from('auditjawabdetail d');
        $this->db->join('auditjawab j', 'j.jwb_id = d.jwb_id', 'inner');
        $this->db->where('j.audit_id', $audit_id);
        $this->db->order_by('j.dtform_id', 'ASC');
        $this->db->order_by('d.dtjwb_id', 'ASC');

        $peta = array();
        foreach ($this->db->get()->result_array() as $b) {
            $peta[(int) $b['dtform_id']][] = $b;
        }

        /* Lampiran: menempel pada jawaban pertanyaan (jwb_id). Bila tabel
           lampiran masih memakai lingkup_id/dtjwb_id (versi lama), ikut
           dibaca supaya tidak ada berkas yang hilang. */
        $this->load->model('LampiranModel', 'lampiran');
        $petaJawaban = $this->lampiran->petaJawaban($audit_id);
        $kolom_butir = $this->lampiran->kolomButir();
        $petaButir = ($kolom_butir && $kolom_butir !== $this->lampiran->kolomJawaban())
            ? $this->lampiran->peta($audit_id)
            : array();

        foreach ($topik as $i => $t) {
            $tid = (int) $t['dtform_id'];
            $butir = isset($peta[$tid]) ? $peta[$tid] : array();
            $jawab_pertanyaan = isset($induk[$tid]) ? $induk[$tid] : NULL;
            $sudah_dijawab = $jawab_pertanyaan
                && trim((string) $jawab_pertanyaan['jwb_jawaban']) !== '';

            $dinilai = 0;
            $temuan = 0;
            $koreksi = 0;
            $jml_lampiran = 0;

            foreach ($butir as $k => $b) {
                $dtjwb_id = (int) $b['dtjwb_id'];

                $butir[$k]['jwb_id']       = (int) $b['jwb_id'];
                $butir[$k]['jwb_jawaban']  = $jawab_pertanyaan ? $jawab_pertanyaan['jwb_jawaban'] : NULL;
                $butir[$k]['jwb_hasil']    = $b['dtjwb_hasil'];
                $butir[$k]['jwb_temuan']   = $b['dtjwb_temuan'];
                $butir[$k]['jwb_catatan']  = $b['dtjwb_catatan'];
                $butir[$k]['jwb_koreksi']  = isset($b['jwb_koreksi']) ? $b['jwb_koreksi'] : '';

                /* Nama kunci lama dipertahankan untuk penampil auditor/admin. */
                $butir[$k]['lingkup_id']   = $dtjwb_id;
                $butir[$k]['lingkup_isi']  = $b['dtjwb_pertanyaan'];
                $butir[$k]['lingkup_teks'] = lingkup_bersihkan($b['dtjwb_pertanyaan']);
                $butir[$k]['dtjwb_teks']   = $butir[$k]['lingkup_teks'];

                if (trim((string) $b['dtjwb_hasil']) !== '')  { $dinilai++; }
                if (trim((string) $b['dtjwb_temuan']) !== '') { $temuan++; }
                if (trim((string) $b['jwb_koreksi']) !== '')  { $koreksi++; }

                $butir[$k]['lampiran'] = isset($petaButir[$dtjwb_id])
                    ? $petaButir[$dtjwb_id]
                    : array();
            }

            $lampiran_topik = $jawab_pertanyaan && isset($petaJawaban[(int) $jawab_pertanyaan['jwb_id']])
                ? $petaJawaban[(int) $jawab_pertanyaan['jwb_id']]
                : array();
            $jml_lampiran += count($lampiran_topik);
            foreach ($butir as $b) {
                $jml_lampiran += count($b['lampiran']);
            }

            $topik[$i]['butir']          = $butir;
            $topik[$i]['jwb']            = $jawab_pertanyaan;
            $topik[$i]['lampiran']       = $lampiran_topik;
            $topik[$i]['sudah_dijawab']  = (bool) $sudah_dijawab;
            $topik[$i]['teks']           = lingkup_bersihkan($t['dtform_pertanyaan']);
            $topik[$i]['jml_butir']      = count($butir);
            $topik[$i]['jml_dinilai']    = $dinilai;
            $topik[$i]['jml_temuan']     = $temuan;
            $topik[$i]['jml_koreksi']    = $koreksi;
            /* Jumlah butir yang "terjawab" (dipakai penampil auditor). */
            $topik[$i]['jml_dijawab']    = $sudah_dijawab ? count($butir) : 0;
            $topik[$i]['jml_lampiran']   = $jml_lampiran;
        }

        return $topik;
    }



    /* =====================================================================
     | DAFTAR PERTANYAAN DARI TABEL mutu_lingkup
     |
     | Halaman detail audit menampilkan BUTIR LINGKUP (mutu_lingkup) sebagai
     | daftar pertanyaan, berikut jawaban auditee untuk tiap butir.
     | Salinan dari modul auditee (baca-saja bagi auditor).
     | Jawaban per butir tersimpan pada mutu_auditjawab dengan kolom
     | lingkup_id terisi; baris pertanyaan (lingkup_id NULL) tetap dipakai
     | sebagai cadangan untuk pertanyaan yang belum punya butir lingkup.
     |
     | Beda dengan modul auditee: mutu_auditjawabdetail (daftar tilik auditor)
     | TIDAK ditampilkan sebagai butir di sini - hanya nilainya yang
     | dilampirkan ke butir lingkup yang cocok.
     * ================================================================== */

    /** Tabel butir lingkup (mutu_lingkup) sudah siap dipakai? */
    function lingkupSiap() {
        return $this->db->table_exists('lingkup');
    }

    /**
     * Satu butir lingkup, dipastikan termasuk formulir audit ini.
     *
     * @return array|NULL
     */
    function butirLingkupAudit($audit_id, $lingkup_id) {
        if (!$this->lingkupSiap() || empty($lingkup_id)) {
            return NULL;
        }

        $this->db->select('lg.lingkup_id, lg.dtform_id, lg.lingkup_isi, lg.lingkup_urut');
        $this->db->from('lingkup lg');
        $this->db->join('detailform dt', 'dt.dtform_id = lg.dtform_id', 'inner');
        $this->db->join('audit au', 'au.form_id = dt.form_id', 'inner');
        $this->db->where('au.audit_id', $audit_id);
        $this->db->where('lg.lingkup_id', $lingkup_id);
        return $this->db->get()->row_array();
    }

    /**
     * Pastikan baris jawaban untuk satu butir lingkup ada, lalu kembalikan
     * jwb_id-nya. Mengembalikan 0 bila butir bukan milik audit ini.
     */
    function pastikanButir($audit_id, $lingkup_id) {
        if (!$this->_adaLingkup()) {
            return 0;
        }

        $butir = $this->butirLingkupAudit($audit_id, $lingkup_id);
        if (!$butir) {
            return 0;
        }

        $this->db->where('audit_id', $audit_id);
        $this->db->where('lingkup_id', $lingkup_id);
        $this->db->order_by('jwb_id', 'asc');
        $baris = $this->db->get('auditjawab')->row_array();
        if ($baris) {
            return (int) $baris['jwb_id'];
        }

        $isi = array(
            'audit_id'   => $audit_id,
            'dtform_id'  => $butir['dtform_id'],
            'lingkup_id' => $lingkup_id,
        );
        if ($this->db->field_exists('jwb_create', 'auditjawab')) {
            $isi['jwb_create'] = date('Y-m-d H:i:s');
        }
        $this->db->insert('auditjawab', $isi);

        return (int) $this->db->insert_id();
    }

    /**
     * Simpan jawaban auditee untuk SATU BUTIR LINGKUP (halaman detail audit).
     */
    function simpanJawabanButir($audit_id, $lingkup_id, $teks) {
        $teks = trim((string) $teks);
        if ($teks === '') {
            return array('status' => FALSE, 'pesan' => 'Jawaban wajib diisi untuk setiap butir lingkup.');
        }

        $butir = $this->butirLingkupAudit($audit_id, $lingkup_id);
        if (!$butir) {
            return array('status' => FALSE, 'pesan' => 'Butir lingkup tidak ditemukan pada audit ini.');
        }

        $jwb_id = $this->pastikanButir($audit_id, $lingkup_id);
        if (!$jwb_id) {
            return array('status' => FALSE, 'pesan' => 'Baris jawaban butir lingkup gagal disiapkan.');
        }

        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $teks = trim(strip_tags($teks, $allowed_tags));

        $isi = array('jwb_jawaban' => $teks);
        if ($this->db->field_exists('jwb_update', 'auditjawab')) {
            $isi['jwb_update'] = date('Y-m-d H:i:s');
        }
        $this->db->where('jwb_id', $jwb_id);
        $this->db->update('auditjawab', $isi);

        return array(
            'status'      => TRUE,
            'pesan'       => 'Jawaban tersimpan.',
            'jwb_id'      => (int) $jwb_id,
            'lingkup_id'  => (int) $lingkup_id,
            'dtform_id'   => (int) $butir['dtform_id'],
            'jwb_jawaban' => $teks,
        );
    }

    /**
     * Peta pertanyaan (topik) + butir lingkup dari tabel mutu_lingkup.
     *
     * Tiap butir lingkup adalah pertanyaan yang DIJAWAB AUDITEE. Selain
     * jawaban, tiap butir memuat penilaian auditor (hasil/temuan/catatan) dan
     * rencana koreksi - keduanya hanya ditampilkan, tidak diisi auditee.
     *
     * Halaman ini HANYA menampilkan butir dari mutu_lingkup. Baris tilik lama
     * (auditjawabdetail) bukan daftar pertanyaan di sini: nilainya (hasil,
     * temuan, catatan, koreksi) cukup dilampirkan ke butir lingkup yang
     * cocok, sedangkan baris yang tidak berpasangan tidak ditampilkan -
     * daftar tilik lengkapnya ada pada halaman Daftar Tilik.
     *
     * @return array daftar topik; tiap topik berisi 'butir', 'punya_lingkup'.
     */
    function petaLingkup($audit_id) {
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
        $peta_butir = $this->lingkupSiap()
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
                    $butir[] = $this->_susunButir($tid, $b, $jawab_butir, $pen, $lampiran_butir, $lampiran_tilik);
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

    /**
     * Ambil (dan tandai sudah dipakai) baris penilaian lama yang cocok
     * dengan butir lingkup ini: berdasar lingkup_id, lalu kemiripan teks.
     */
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

    /** Susun satu butir lingkup beserta jawaban auditee dan lampiran. */
    private function _susunButir($dtform_id, $b, $jawab_butir, $pen, $lampiran_butir, $lampiran_tilik) {
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
