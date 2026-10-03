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
        $this->db->select("(SELECT jwb_jawaban from mutu_auditjawab where audit_id = au.audit_id AND dtform_id = dt.dtform_id" . $this->_lingkupNull() . ") as jwb_jawaban");
        $this->db->from('audit au');
        $this->db->join('detailform dt', 'dt.form_id = au.form_id', 'left');
        $this->db->where('au.audit_id',$id);
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
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id) {
        $this->db->from('audit');
        $this->db->where('audit.audit_id',$id);
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

}
