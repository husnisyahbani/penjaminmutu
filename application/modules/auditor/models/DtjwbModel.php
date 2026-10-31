<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Daftar tilik (delik) audit.
 *
 * Sumber datanya adalah tabel `mutu_auditjawabdetail` (baris butir tilik yang
 * dibuat/diisi auditor), dihubungkan ke `mutu_auditjawab` melalui `jwb_id`:
 *
 *   auditjawab        -> baris pertanyaan (jawaban auditee, tujuan, koreksi)
 *   auditjawabdetail  -> baris butir tilik: dtjwb_pertanyaan, dtjwb_hasil,
 *                        dtjwb_temuan, dtjwb_catatan
 *
 * Halaman auditee (PTK & delik) membaca tabel yang sama, sehingga apa yang
 * diisi auditor langsung tampil di sana.
 */
class DtjwbModel extends CI_Model {

    var $column_search = array('dj.dtjwb_referensi','dj.dtjwb_pertanyaan','dj.dtjwb_hasil','dj.dtjwb_temuan','dj.dtjwb_catatan');
    var $column_order = array(null,'dj.dtjwb_referensi','dj.dtjwb_pertanyaan','dj.dtjwb_temuan','dj.dtjwb_hasil','dj.dtjwb_catatan',null);

    function __construct() {
        parent::__construct();
    }

    /**
     * Baris induk (data pertanyaan) untuk satu audit + pertanyaan.
     * Dibuat bila belum ada supaya butir tilik selalu punya tempat berpijak.
     */
    function pastikanInduk($audit_id, $dtform_id) {
        /* Pertanyaan harus benar-benar milik formulir audit ini, supaya tidak
           membuat baris pertanyaan asing (mis. saat dtform_id diketik manual). */
        $this->db->select('dt.dtform_id');
        $this->db->from('detailform dt');
        $this->db->join('audit au', 'au.form_id = dt.form_id', 'inner');
        $this->db->where('au.audit_id', $audit_id);
        $this->db->where('dt.dtform_id', $dtform_id);
        if (!$this->db->get()->row_array()) {
            return 0;
        }

        $this->db->where('audit_id', $audit_id);
        $this->db->where('dtform_id', $dtform_id);
        /* Baris pertanyaan = baris tanpa butir lingkup. Kolomnya hanya ada
           bila migrasi lingkup pernah dijalankan. */
        if ($this->db->field_exists('lingkup_id', 'auditjawab')) {
            $this->db->where('lingkup_id IS NULL', NULL, FALSE);
        }
        $this->db->order_by('jwb_id', 'asc');
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

    /** Query dasar daftar tilik: baris butir dari auditjawabdetail. */
    private function _get_datatables_query($search, $ordering, $jwb_id) {
        $this->db->select('dj.dtjwb_id, dj.dtjwb_referensi, dj.dtjwb_pertanyaan');
        $this->db->select('dj.dtjwb_hasil, dj.dtjwb_temuan, dj.dtjwb_catatan');
        /* Alias nama lama supaya penampil (controller/view) yang memakai
           jwb_hasil/jwb_temuan/jwb_catatan tetap membaca nilainya. */
        $this->db->select('dj.dtjwb_hasil AS jwb_hasil, dj.dtjwb_temuan AS jwb_temuan,'
            . ' dj.dtjwb_catatan AS jwb_catatan', FALSE);
        /* Jawaban auditee: kolom per butir bila ada (data lama), jika tidak
           dipakai jawaban pertanyaan (jwb_jawaban) sebagai cadangan. */
        if ($this->db->field_exists('dtjwb_jawaban', 'auditjawabdetail')) {
            $this->db->select("NULLIF(dj.dtjwb_jawaban, '') AS jwb_jawaban_butir", FALSE);
        } else {
            $this->db->select('NULL AS jwb_jawaban_butir', FALSE);
        }
        $this->db->select('j.jwb_jawaban AS jwb_jawaban_pertanyaan', FALSE);
        /* Penanda butir lingkup (mutu_lingkup) pada baris tilik lama. */
        if ($this->db->field_exists('lingkup_id', 'auditjawabdetail')) {
            $this->db->select('dj.lingkup_id AS dtjwb_lingkup_id', FALSE);
        }
        $this->db->from('auditjawabdetail dj');
        $this->db->join('auditjawab j', 'j.jwb_id = dj.jwb_id', 'left');
        $this->db->where('dj.jwb_id', $jwb_id);

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
            $this->db->order_by('dj.dtjwb_id', 'asc');
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

    /** Butir tilik + nilainya, tanpa paging (dipakai hitungan & ekspor). */
    function butirAudit($jwb_id) {
        $this->_get_datatables_query(array('value' => ''), array(), $jwb_id);
        return $this->_lengkapiJawaban($this->db->get()->result_array(), $jwb_id);
    }

    /**
     * Lengkapi jawaban auditee PER BUTIR LINGKUP pada baris tilik.
     *
     * Auditee menjawab tiap butir lingkup (mutu_auditjawab.lingkup_id), bukan
     * baris tilik auditor. Jawaban itu dilampirkan ke baris tilik yang cocok:
     *   1. lewat kolom auditjawabdetail.lingkup_id,
     *   2. lewat kecocokan teks butir (data lama yang belum dimigrasi),
     *   3. sisanya tetap memakai jawaban tingkat pertanyaan.
     */
    private function _lengkapiJawaban($rows, $jwb_id) {
        if (empty($rows)) {
            return $rows;
        }

        $this->load->helper('lingkup');

        $induk = $this->db->select('audit_id')
            ->where('jwb_id', $jwb_id)->get('auditjawab')->row_array();
        if (!$induk) {
            return $rows;
        }

        $peta = $this->_jawabanLingkup((int) $induk['audit_id']);
        if (empty($peta['id']) && empty($peta['teks'])) {
            return $rows;
        }

        foreach ($rows as $k => $r) {
            $jawaban = '';
            $lid = isset($r['dtjwb_lingkup_id']) ? (int) $r['dtjwb_lingkup_id'] : 0;

            if ($lid > 0 && isset($peta['id'][$lid])) {
                $jawaban = $peta['id'][$lid];
            }
            if ($jawaban === '') {
                $kunci = lingkup_normal($r['dtjwb_pertanyaan']);
                if ($kunci !== '' && isset($peta['teks'][$kunci])) {
                    $jawaban = $peta['teks'][$kunci];
                }
            }
            if ($jawaban !== '') {
                $rows[$k]['jwb_jawaban_butir'] = $jawaban;
            }
        }

        return $rows;
    }

    /** Peta jawaban per butir lingkup: berdasar lingkup_id dan teks butir. */
    private function _jawabanLingkup($audit_id) {
        $hasil = array('id' => array(), 'teks' => array());

        if (!$this->db->field_exists('lingkup_id', 'auditjawab') || !$this->db->table_exists('lingkup')) {
            return $hasil;
        }

        $this->db->select('jb.jwb_jawaban, jb.lingkup_id, lg.lingkup_isi');
        $this->db->from('auditjawab jb');
        $this->db->join('lingkup lg', 'lg.lingkup_id = jb.lingkup_id', 'inner');
        $this->db->where('jb.audit_id', $audit_id);
        $this->db->where('jb.lingkup_id IS NOT NULL', NULL, FALSE);
        foreach ($this->db->get()->result_array() as $j) {
            if (trim((string) $j['jwb_jawaban']) === '') {
                continue;
            }
            $hasil['id'][(int) $j['lingkup_id']] = $j['jwb_jawaban'];
            $kunci = lingkup_normal($j['lingkup_isi']);
            if ($kunci !== '') {
                $hasil['teks'][$kunci] = $j['jwb_jawaban'];
            }
        }

        return $hasil;
    }

    /** Satu baris butir tilik (untuk modal edit). */
    function getNilai($jwb_id, $dtjwb_id) {
        $this->db->where('jwb_id', $jwb_id);
        $this->db->where('dtjwb_id', $dtjwb_id);
        return $this->db->get('auditjawabdetail')->row_array();
    }

    /** Nama lama, dipertahankan untuk pemanggil yang belum diperbarui. */
    function getNilaiByLingkup($jwb_id, $dtjwb_id) {
        return $this->getNilai($jwb_id, $dtjwb_id);
    }

    /**
     * Simpan satu nilai (hasil/temuan/catatan) pada satu baris tilik.
     * Kolom jwb_* dipetakan ke kolom dtjwb_* pada auditjawabdetail.
     */
    function simpanNilai($jwb_id, $dtjwb_id, $kolom, $nilai) {
        $peta = array(
            'jwb_hasil'   => 'dtjwb_hasil',
            'jwb_temuan'  => 'dtjwb_temuan',
            'jwb_catatan' => 'dtjwb_catatan',
        );
        if (!isset($peta[$kolom]) || empty($dtjwb_id)) {
            return FALSE;
        }

        $baris = $this->getNilai($jwb_id, $dtjwb_id);
        if (!$baris) {
            return FALSE;
        }

        $this->db->where('dtjwb_id', $dtjwb_id);
        $this->db->update('auditjawabdetail', array($peta[$kolom] => $nilai));

        return $this->db->affected_rows() >= 0;
    }

    /** Baris induk berdasarkan jwb_id. */
    private function induk($jwb_id) {
        $this->db->where('jwb_id', $jwb_id);
        return $this->db->get('auditjawab')->row_array();
    }

    /* ----------- tambah / ubah / hapus butir tilik ----------- */

    function getjawabById($dtjwb_id) {
        $this->db->where('dtjwb_id', $dtjwb_id);
        return $this->db->get('auditjawabdetail')->row_array();
    }

    /** Tambah satu baris butir tilik pada satu pertanyaan. */
    function tambah($jwb_id, $pertanyaan, $referensi = NULL) {
        $this->db->insert('auditjawabdetail', array(
            'jwb_id'           => $jwb_id,
            'dtjwb_pertanyaan' => $pertanyaan,
            'dtjwb_referensi'  => $referensi,
        ));
        return $this->db->insert_id();
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
