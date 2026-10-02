<?php

class Delik extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'auditor';
        $this->load->js(base_url("assets/app/auditor/delik.js?v=5.0"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('DtjwbModel', 'dtjwb');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');
        // Pembantu teks butir (lingkup_bersihkan) untuk daftar tilik.
        $this->load->helper('lingkup');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'AUDITOR') {
            redirect(base_url());
        }
    }

    public function index() {
        $audit_id = $this->input->get('audit_id');
        $dtform_id = $this->input->get('dtform_id');
        if(isset($audit_id) && isset($dtform_id)){
            $this->data['content'] = 'delik/indexnew';
            $this->data['title'] = 'Daftar Tilik';
            $this->data['audit_id'] = $audit_id;
            $this->data['dtform_id'] = $dtform_id;
            $this->data['jwb_id'] = $this->dtjwb->getJwbid($audit_id,$dtform_id);
            $this->data['result'] = $this->mutu->getAuditById($audit_id);
            $this->data['jawab'] = $this->auditjawab->getAuditJawab($audit_id,$dtform_id);
            $this->data['soal'] = $this->formulir->getSoalFormulir($dtform_id);

            /* Ringkasan kartu: jumlah butir tilik per jenis penilaian.
               Sumbernya tabel yang sama dengan tabel di bawah. */
            $butir = $this->dtjwb->butirAudit($this->data['jwb_id']);
            $ringkas = array('total' => count($butir), 'sesuai' => 0, 'observasi' => 0,
                             'minor' => 0, 'mayor' => 0);
            foreach ($butir as $b) {
                switch (strtoupper(trim((string) $b['dtjwb_temuan']))) {
                    case 'S':        $ringkas['sesuai']++;    break;
                    case 'OB':       $ringkas['observasi']++; break;
                    case 'TS MINOR': $ringkas['minor']++;     break;
                    case 'TS MAYOR': $ringkas['mayor']++;     break;
                }
            }
            $this->data['ringkas'] = $ringkas;

            $this->data['js'] = $this->load->get_js_files();
            $this->data['audit'] = 'active';
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
            
        }
    }

    public function tujuan() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_tujuan'] = $this->input->post('jwb_tujuan');
        $allowed_tags = '<p><br><b><i><u><strong><em><ul><ol><li>';
        $data['jwb_tujuan'] = strip_tags($data['jwb_tujuan'], $allowed_tags);
        if($this->auditjawab->is_exist($data)){
            $status = $this->auditjawab->jawab($data);
        }else{
            $status = $this->auditjawab->add($data);
        }
        
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    /**
     * Satu butir tilik (untuk modal edit) - dibaca dari
     * mutu_auditjawabdetail lewat jwb_id + dtjwb_id.
     */
    public function gettilik($jwb_id = NULL, $dtjwb_id = NULL) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $nilai = $this->dtjwb->getNilai($jwb_id, $dtjwb_id);
        if (!$nilai) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Butir tilik tidak ditemukan.'));
            return;
        }

        $nilai['status']      = TRUE;
        /* Nama kunci lama (dipakai berkas JS) dipetakan dari kolom dtjwb_*. */
        $nilai['lingkup_isi'] = $nilai['dtjwb_pertanyaan'];
        $nilai['jwb_hasil']   = $nilai['dtjwb_hasil'];
        $nilai['jwb_temuan']  = $nilai['dtjwb_temuan'];
        $nilai['jwb_catatan'] = $nilai['dtjwb_catatan'];
        echo json_encode($nilai);
    }

    /** Tambah satu butir tilik pada pertanyaan ini. */
    public function tambahtilik() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $jwb_id     = $this->input->post('jwb_id');
        $pertanyaan = $this->input->post('dtjwb_pertanyaan');
        $referensi  = $this->input->post('dtjwb_referensi');

        $id = (!empty($jwb_id) && trim((string) $pertanyaan) !== '')
            ? $this->dtjwb->tambah($jwb_id, $pertanyaan, $referensi)
            : 0;

        echo json_encode($id
            ? array('status' => TRUE, 'pesan' => 'Butir tilik ditambahkan.', 'dtjwb_id' => $id)
            : array('status' => FALSE, 'pesan' => 'Gagal menambah butir tilik.'));
    }

    /** Ubah teks/referensi satu butir tilik. */
    public function pertanyaan() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $dtjwb_id = $this->input->post('pertanyaan_dtjwb_id');
        if (empty($dtjwb_id)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Butir tilik tidak dikenal.'));
            return;
        }

        $status = $this->dtjwb->edit(array(
            'dtjwb_id'          => $dtjwb_id,
            'dtjwb_pertanyaan'  => $this->input->post('edit_dtjwb_pertanyaan'),
            'dtjwb_referensi'   => $this->input->post('edit_dtjwb_referensi'),
        ));
        echo json_encode(array('status' => $status));
    }

    /** Hapus satu butir tilik. */
    public function hapus() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $dtjwb_id = $this->input->post('dtjwb_id');
        $status   = !empty($dtjwb_id) ? $this->dtjwb->hapus($dtjwb_id) : FALSE;
        echo json_encode(array('status' => $status));
    }

    /**
     * Simpan Hasil / Temuan / Catatan satu butir tilik
     * (mutu_auditjawabdetail.dtjwb_*).
     * Data: jwb_id, dtjwb_id, kolom (jwb_hasil|jwb_temuan|jwb_catatan), nilai
     */
    public function simpantilik() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $jwb_id   = $this->input->post('jwb_id');
        $dtjwb_id = $this->input->post('dtjwb_id');
        $kolom    = $this->input->post('kolom');
        $nilai    = $this->input->post('nilai');

        $diizinkan = array('jwb_hasil', 'jwb_temuan', 'jwb_catatan');
        if (!in_array($kolom, $diizinkan, TRUE)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Kolom tidak dikenal.'));
            return;
        }

        // Temuan memakai pilihan tetap.
        if ($kolom === 'jwb_temuan' && $nilai !== '' && !in_array($nilai, array('S', 'OB', 'TS MINOR', 'TS MAYOR'), TRUE)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Temuan tidak dikenal.'));
            return;
        }

        $status = $this->dtjwb->simpanNilai($jwb_id, $dtjwb_id, $kolom, $nilai);
        echo json_encode($status
            ? array('status' => TRUE, 'pesan' => 'Tersimpan.')
            : array('status' => FALSE, 'pesan' => 'Gagal menyimpan.'));
    }

    /**
     * Ringkasan kartu (Sesuai / Observasi / Minor / Mayor) untuk satu
     * pertanyaan - dipakai setelah tambah/ubah/hapus butir.
     */
    public function ringkasan($jwb_id = NULL) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $butir = $this->dtjwb->butirAudit($jwb_id);
        $total = count($butir);
        $hitung = array('S' => 0, 'OB' => 0, 'TS MINOR' => 0, 'TS MAYOR' => 0);
        foreach ($butir as $b) {
            $kunci = strtoupper(trim((string) $b['dtjwb_temuan']));
            if (isset($hitung[$kunci])) {
                $hitung[$kunci]++;
            }
        }

        $persen = function ($nilai) use ($total) {
            return $total < 1 ? 0 : (int) round($nilai * 100 / $total);
        };

        $kartu = array();
        foreach (array('S' => 'sesuai', 'OB' => 'observasi', 'TS MINOR' => 'minor', 'TS MAYOR' => 'mayor') as $kode => $nama) {
            $kartu[] = array('nama' => $nama, 'nilai' => $hitung[$kode], 'bar' => $persen($hitung[$kode]));
        }

        echo json_encode(array(
            'status' => TRUE,
            'total'  => $total,
            'kartu'  => $kartu,
        ));
    }

    /**
     * Simpan satu kolom isian butir tilik (dtjwb_hasil | dtjwb_temuan |
     * dtjwb_catatan). Dipakai ikon edit pada masing-masing kolom tabel.
     */
    public function simpanbutir() {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $jwb_id   = $this->input->post('jwb_id');
        $dtjwb_id = $this->input->post('dtjwb_id');
        $kolom    = $this->input->post('kolom');
        $nilai    = $this->input->post('nilai');

        $diizinkan = array('dtjwb_hasil', 'dtjwb_temuan', 'dtjwb_catatan');
        if (!in_array($kolom, $diizinkan, TRUE)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Kolom tidak dikenal.'));
            return;
        }

        if ($kolom === 'dtjwb_temuan' && $nilai !== ''
                && !in_array($nilai, array('S', 'OB', 'TS MINOR', 'TS MAYOR'), TRUE)) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Temuan tidak dikenal.'));
            return;
        }

        $baris = $this->dtjwb->getNilai($jwb_id, $dtjwb_id);
        if (!$baris) {
            echo json_encode(array('status' => FALSE, 'pesan' => 'Butir tilik tidak ditemukan.'));
            return;
        }

        $status = $this->dtjwb->edit(array('dtjwb_id' => $dtjwb_id, $kolom => $nilai));
        echo json_encode($status
            ? array('status' => TRUE, 'pesan' => 'Tersimpan.')
            : array('status' => FALSE, 'pesan' => 'Gagal menyimpan.'));
    }

    public function listdelik($id) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->dtjwb->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        $warna = array('S' => 'badge-success', 'OB' => 'badge-info',
                       'TS MINOR' => 'badge-warning', 'TS MAYOR' => 'badge-danger');
        foreach ($list as $field) {
            $no++;
            $temuan = strtoupper(trim((string) $field->dtjwb_temuan));
            $kelas  = isset($warna[$temuan]) ? $warna[$temuan] : 'badge-default';

            /* Referensi dituliskan di bawah isian pada kolom Butir Lingkup. */
            $butir = '<div class="tilik-butir">' . html_escape(lingkup_bersihkan($field->dtjwb_pertanyaan));
            if (trim((string) $field->dtjwb_referensi) !== '') {
                $butir .= '<div class="tilik-ref"><strong>Referensi:</strong> '
                    . html_escape(lingkup_bersihkan($field->dtjwb_referensi)) . '</div>';
            }
            $butir .= '</div>';

            $row = array();
            $row[] = $no;
            $row[] = $butir;
            $row[] = '<div class="tilik-nilai">'
                . '<span class="tilik-teks">' . html_escape(lingkup_bersihkan($field->dtjwb_hasil)) . '</span>'
                . ' <button type="button" class="editisi btn btn-xs btn-icon btn-primary"'
                . ' data-info="Ubah hasil" dtjwb_id="' . (int) $field->dtjwb_id . '"'
                . ' data-kolom="dtjwb_hasil" data-nilai="' . html_escape($field->dtjwb_hasil) . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button></div>';
            $row[] = '<div class="tilik-nilai">'
                . '<span class="badge ' . $kelas . '">' . html_escape($temuan) . '</span>'
                . ' <button type="button" class="editisi btn btn-xs btn-icon btn-warning"'
                . ' data-info="Ubah temuan" dtjwb_id="' . (int) $field->dtjwb_id . '"'
                . ' data-kolom="dtjwb_temuan" data-nilai="' . html_escape($temuan) . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button></div>';
            $row[] = '<div class="tilik-nilai">'
                . '<span class="tilik-teks">' . html_escape(lingkup_bersihkan($field->dtjwb_catatan)) . '</span>'
                . ' <button type="button" class="editisi btn btn-xs btn-icon btn-success"'
                . ' data-info="Ubah catatan" dtjwb_id="' . (int) $field->dtjwb_id . '"'
                . ' data-kolom="dtjwb_catatan" data-nilai="' . html_escape($field->dtjwb_catatan) . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button></div>';

            /* Kolom Butir Lingkup disunting lewat ikon di dalam selnya: satu
               formulir mengubah pertanyaan sekaligus referensinya. */
            $row[1] = '<div class="tilik-butir-kotak">' . $butir
                . ' <button type="button" class="editbutir btn btn-xs btn-icon btn-primary"'
                . ' data-info="Ubah pertanyaan & referensi" dtjwb_id="' . (int) $field->dtjwb_id . '"'
                . ' data-pertanyaan="' . html_escape($field->dtjwb_pertanyaan) . '"'
                . ' data-referensi="' . html_escape($field->dtjwb_referensi) . '">'
                . '<i class="icon md-edit" aria-hidden="true"></i></button></div>';

            /* Aksi: tombol hapus saja. */
            $row[] = '<div class="tabel-aksi">'
                . '<button type="button" class="hapustilik btn btn-sm btn-icon btn-danger"'
                . ' data-info="Hapus butir tilik ini" dtjwb_id="' . (int) $field->dtjwb_id . '">'
                . '<i class="icon md-delete" aria-hidden="true"></i></button></div>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->dtjwb->count_all($id),
            "recordsFiltered" => $this->dtjwb->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

}
