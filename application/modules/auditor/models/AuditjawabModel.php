<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AuditjawabModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    var $column_search = array('dtform_pertanyaan','jwb_jawaban','jwb_hasil','jwb_temuan','jwb_catatan');
    /* Lingkup kini berupa daftar butir (ditampilkan dari tabel lingkup). */
    var $column_order = array(null,'dtform_pertanyaan','dtform_pertanyaan','jwb_jawaban','jwb_hasil','jwb_temuan','jwb_catatan');
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
        $this->db->select("(SELECT jwb_tujuan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_tujuan");
        $this->db->select("(SELECT jwb_referensi from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_referensi");
        $this->db->select("(SELECT jwb_pertanyaan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_pertanyaan");
        $this->db->select("(SELECT jwb_catatan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_catatan");
        $this->db->select("(SELECT jwb_temuan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_temuan");
        $this->db->select("(SELECT jwb_hasil from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_hasil");
        $this->db->select("(SELECT jwb_jawaban from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_jawaban");
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
        $this->db->select("(SELECT jwb_catatan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_catatan");
        $this->db->select("(SELECT jwb_temuan from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_temuan");
        $this->db->select("(SELECT jwb_hasil from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_hasil");
        $this->db->select("(SELECT jwb_jawaban from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id AND lingkup_id IS NULL) as jwb_jawaban");
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
        if (!array_key_exists('lingkup_id', $data)) {
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
        $this->db->where($this->db->dbprefix('auditjawab').'.lingkup_id IS NULL', NULL, FALSE);
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
        $this->db->where($this->db->dbprefix('auditjawab').'.lingkup_id IS NULL', NULL, FALSE);
        $query = $this->db->get('auditjawab');
        return $query->row_array();
    }

     public function jawab($data) {
        $this->db->trans_start();
        $this->db->where("audit_id",$data['audit_id']);
        $this->db->where("dtform_id",$data['dtform_id']);
        $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        $this->db->update('auditjawab',$data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function is_exist($data) {
        
        $this->db->where("audit_id",$data['audit_id']);
        $this->db->where("dtform_id",$data['dtform_id']);
        $this->db->where('lingkup_id IS NULL', NULL, FALSE);
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
     * Baris daftar tilik satu pertanyaan, digabung dari struktur baru
     * (butir lingkup + auditjawab) dan sisa data lama yang belum terpetakan.
     * Kunci keluaran mengikuti nama lama (dtjwb_*) agar tampilan/ekspor tetap jalan.
     */
    public function barisTilik($audit_id, $dtform_id) {
        $this->load->model('LingkupModel', 'lingkup');

        $baris = array();

        // a) butir lingkup struktur baru
        $this->db->select('lg.lingkup_id, lg.lingkup_isi');
        $this->db->select('jb.jwb_hasil, jb.jwb_temuan, jb.jwb_catatan, jb.jwb_koreksi');
        $this->db->from('lingkup lg');
        $this->db->join('auditjawab jb', 'jb.audit_id = ' . (int) $audit_id . ' AND jb.lingkup_id = lg.lingkup_id', 'left');
        $this->db->where('lg.dtform_id', $dtform_id);
        $this->db->order_by('lg.lingkup_urut', 'asc');
        $this->db->order_by('lg.lingkup_id', 'asc');
        foreach ($this->db->get()->result_array() as $b) {
            $baris[] = array(
                'dtjwb_id'         => $b['lingkup_id'],
                'dtjwb_referensi'  => '',
                'dtjwb_pertanyaan' => $b['lingkup_isi'],
                'dtjwb_hasil'      => $b['jwb_hasil'],
                'dtjwb_temuan'     => $b['jwb_temuan'],
                'dtjwb_catatan'    => $b['jwb_catatan'],
                'dtjwb_koreksi'    => $b['jwb_koreksi'],
                'lingkup_id'       => $b['lingkup_id'],
                'baris'            => 'butir',
            );
        }

        // b) sisa baris lama yang belum dipetakan ke butir mana pun
        $jwb = $this->getAuditJawabFix($audit_id, $dtform_id);
        if (!empty($jwb['jwb_id'])) {
            $this->db->where('jwb_id', $jwb['jwb_id']);
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
            foreach ($this->db->get('auditjawabdetail')->result_array() as $d) {
                /* Baris lama tidak punya kolom koreksi (kolom itu ada pada
                   auditjawab.jwb_koreksi) dan boleh jadi tidak punya kolom
                   referensi. Lengkapi kuncinya agar bentuknya sama dengan
                   baris butir, sehingga pemakai tidak menemui "undefined
                   index" dan koreksi lama tetap terbaca. */
                $d['dtjwb_referensi'] = isset($d['dtjwb_referensi']) ? $d['dtjwb_referensi'] : '';
                $d['dtjwb_koreksi']   = isset($jwb['jwb_koreksi']) ? $jwb['jwb_koreksi'] : '';
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
    function petaTilik($audit_id) {
        $this->load->model('LingkupModel', 'lingkup');

        $audit = $this->db->select('audit_id, form_id')
            ->where('audit_id', $audit_id)
            ->get('audit')->row_array();
        if (!$audit) {
            return array();
        }

        $this->db->where('form_id', $audit['form_id']);
        if ($this->db->field_exists('dtform_urut', 'detailform')) {
            $this->db->order_by('dtform_urut', 'ASC');
        }
        $this->db->order_by('dtform_id', 'ASC');
        $topik = $this->db->get('detailform')->result_array();
        if (!$topik) {
            return array();
        }

        $ids = array();
        foreach ($topik as $t) {
            $ids[] = $t['dtform_id'];
        }
        $peta = $this->lingkup->peta($ids);

        // Lampiran jawaban auditee (opsional, boleh lebih dari satu).
        $this->load->model('LampiranModel', 'lampiran');
        $petaLampiran = $this->lampiran->peta($audit_id);

        // Jawaban per butir (lingkup_id terisi).
        $this->db->select('jwb_id, dtform_id, lingkup_id, jwb_jawaban, jwb_hasil, jwb_temuan, jwb_catatan, jwb_koreksi');
        $this->db->where('audit_id', $audit_id);
        $this->db->where('lingkup_id IS NOT NULL', NULL, FALSE);
        $jawab = array();
        foreach ($this->db->get('auditjawab')->result_array() as $j) {
            $jawab[(int) $j['lingkup_id']] = $j;
        }

        // Jawaban tingkat pertanyaan (lingkup_id NULL): jawaban auditee + tujuan.
        $this->db->select('jwb_id, dtform_id, jwb_jawaban, jwb_tujuan, jwb_referensi');
        $this->db->where('audit_id', $audit_id);
        $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        $induk = array();
        foreach ($this->db->get('auditjawab')->result_array() as $j) {
            $induk[(int) $j['dtform_id']] = $j;
        }

        foreach ($topik as $i => $t) {
            $tid = (int) $t['dtform_id'];

            $butir = isset($peta[$tid]) ? $peta[$tid] : array();
            $dinilai = 0;
            $temuan = 0;
            $koreksi = 0;
            $dijawab = 0;
            $jml_lampiran = 0;

            foreach ($butir as $k => $b) {
                $lj = isset($jawab[(int) $b['lingkup_id']]) ? $jawab[(int) $b['lingkup_id']] : NULL;

                $butir[$k]['jwb_id']      = $lj ? $lj['jwb_id'] : NULL;
                $butir[$k]['jwb_jawaban'] = $lj ? $lj['jwb_jawaban'] : NULL;
                $butir[$k]['jwb_hasil']   = $lj ? $lj['jwb_hasil'] : NULL;
                $butir[$k]['jwb_temuan']  = $lj ? $lj['jwb_temuan'] : NULL;
                $butir[$k]['jwb_catatan'] = $lj ? $lj['jwb_catatan'] : NULL;
                $butir[$k]['jwb_koreksi'] = $lj ? $lj['jwb_koreksi'] : NULL;
                $butir[$k]['lingkup_teks'] = lingkup_bersihkan($b['lingkup_isi']);

                if ($lj && trim((string) $lj['jwb_hasil']) !== '') {
                    $dinilai++;
                }
                if ($lj && trim((string) $lj['jwb_temuan']) !== '') {
                    $temuan++;
                }
                if ($lj && trim((string) $lj['jwb_koreksi']) !== '') {
                    $koreksi++;
                }
                if ($lj && trim((string) $lj['jwb_jawaban']) !== '') {
                    $dijawab++;
                }

                $butir[$k]['lampiran'] = isset($petaLampiran[(int) $b['lingkup_id']])
                    ? $petaLampiran[(int) $b['lingkup_id']]
                    : array();
                $jml_lampiran += count($butir[$k]['lampiran']);
            }

            $topik[$i]['butir']        = $butir;
            $topik[$i]['jwb']          = isset($induk[$tid]) ? $induk[$tid] : NULL;
            $topik[$i]['teks']         = lingkup_bersihkan($t['dtform_pertanyaan']);
            $topik[$i]['jml_butir']    = count($butir);
            $topik[$i]['jml_dinilai']   = $dinilai;
            $topik[$i]['jml_temuan']    = $temuan;
            $topik[$i]['jml_koreksi']   = $koreksi;
            $topik[$i]['jml_dijawab']   = $dijawab;
            $topik[$i]['jml_lampiran']  = $jml_lampiran;
        }

        return $topik;
    }

}
