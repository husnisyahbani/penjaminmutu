<?php

class Daftaraudit extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'admin';
        $this->load->js(base_url("assets/app/admin/daftaraudit.js?v=1.39"));
        $this->load->model('AuditjawabModel', 'auditjawab');
        $this->load->model('AuditJawabDetailModel', 'auditjawabdetail');
        $this->load->model('MutuauditModel', 'mutu');
        $this->load->model('DtformModel', 'dtform');
        $this->load->model('AkunModel', 'akun');
        $this->load->model('FormulirModel', 'formulir');

        $role = $this->session->userdata('role');
        if (!isset($role) || $role != 'PPM') {
            redirect(base_url());
        }
    }

    public function tes(){
        $audit = $this->mutu->getAuditById("43");
        $dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
        foreach($dtform as $value){
            $jwb = $this->auditjawab->getAuditJawabFix($audit['audit_id'],$value['dtform_id']);
            $detail = $this->auditjawabdetail->getAuditJawabDetail($jwb['jwb_id']);
            echo "<pre>";
            print_r($jwb);
            echo "</pre>";  
        } 
    }

    public function download()
{
   require_once APPPATH.'third_party/PHPExcel.php';

    $ids = $this->input->post('ids');
    if (!isset($ids) || count($ids) == 0) {
        $this->session->set_flashdata('pesanerror', 'Tidak ada data yang dipilih!');
            redirect(base_url('admin/daftaraudit'));
    }

    $excel = new PHPExcel();
    $tahun = date('Y');

    /* =====================================================
     * SHEET 1 : COVER
     * ===================================================== */
    $excel->setActiveSheetIndex(0);
    $sheetCover = $excel->getActiveSheet();
    $sheetCover->setTitle('Cover');

    // Ambil audit pertama (untuk cover)
    $audit = $this->mutu->getAuditById($ids[0]);

    $tanggal = date("d", strtotime($audit['audit_update']));
    $bulan   = date("m", strtotime($audit['audit_update']));
    $tahun   = date("Y", strtotime($audit['audit_update']));
    $tglAudit = $tanggal . " " . konvbln($bulan) . " " . $tahun;

    // Layout cover
    $sheetCover->mergeCells('A1:J3');
    $sheetCover->mergeCells('A5:J5');
    $sheetCover->mergeCells('A6:J6');
    $sheetCover->mergeCells('A7:J7');

    $sheetCover->setCellValue('A1', $audit['form_nama']);
    $sheetCover->setCellValue('A5', 'PUSAT PENJAMINAN MUTU');
    $sheetCover->setCellValue('A6', 'SEKOLAH TINGGI ILMU KESEHATAN SITI KHADIJAH');
    $sheetCover->setCellValue('A7', 'TAHUN '.$tahun);

    $sheetCover->getStyle('A1')->getFont()->setSize(22)->setBold(true);
    $sheetCover->getStyle('A5:A7')->getFont()->setSize(14)->setBold(true);

    $sheetCover->getStyle('A1:A7')->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    // Logo
    $logoPath = FCPATH . 'filedata/logostikfinal.png';
    if (file_exists($logoPath)) {
        $logo = new PHPExcel_Worksheet_Drawing();
        $logo->setName('Logo STIK');
        $logo->setPath($logoPath);
        $logo->setHeight(160);
        $logo->setCoordinates('E9');
        $logo->setWorksheet($sheetCover);
    }

    /* =====================================================
     * SHEET 2 : FORM AUDIT
     * ===================================================== */
    $sheet = $excel->createSheet();
    $sheet->setTitle('Form Audit');
    $excel->setActiveSheetIndex(1);

    // Header form
    $sheet->mergeCells('A1:J1');
    $sheet->setCellValue('A1', $audit['form_nama']);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('A3', 'Tanggal Audit');
    $sheet->setCellValue('C3', $tglAudit);
    $sheet->setCellValue('A4', 'Unit Kerja');
    $sheet->setCellValue('C4', $audit['unit']);
    $sheet->setCellValue('A5', 'Auditor');
    $sheet->setCellValue('C5', $audit['auditor']);
    $sheet->setCellValue('A6', 'Auditee');
    $sheet->setCellValue('C6', $audit['auditee']);

    // Header tabel
    $startRow = 8;
    $header = [
        'No','Tujuan','Referensi','Pertanyaan','Hasil Observasi',
        'S','OB','TS Minor','TS Mayor','Catatan'
    ];

    $col = 'A';
    foreach ($header as $h) {
        $sheet->setCellValue($col.$startRow, $h);
        $sheet->getStyle($col.$startRow)->getFont()->setBold(true);
        $sheet->getStyle($col.$startRow)
      ->getAlignment()
      ->setWrapText(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Data
    $rowExcel = $startRow + 1;
    $no = 1;

    foreach ($ids as $id) {

        $audit = $this->mutu->getAuditById($id);
        $dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);

        foreach ($dtform as $row) {

            $jwb = $this->auditjawab->getAuditJawabFix(
                $audit['audit_id'],
                $row['dtform_id']
            );

            if (!$jwb) continue;

            $detail = $this->auditjawabdetail
                           ->getAuditJawabDetail($jwb['jwb_id']);

            foreach ($detail as $dtjwb) {

                $S = $OB = $TSMIN = $TSMAY = '';

                if ($dtjwb['dtjwb_temuan'] == 'S') $S = 'X';
                if ($dtjwb['dtjwb_temuan'] == 'OB') $OB = 'X';
                if ($dtjwb['dtjwb_temuan'] == 'TS MINOR') $TSMIN = 'X';
                if ($dtjwb['dtjwb_temuan'] == 'TS MAYOR') $TSMAY = 'X';

                $sheet->fromArray([
                    $no,
                    strip_tags($jwb['jwb_tujuan']),
                    $dtjwb['dtjwb_referensi'],
                    $dtjwb['dtjwb_pertanyaan'],
                    $dtjwb['dtjwb_hasil'],
                    $S, $OB, $TSMIN, $TSMAY,
                    $dtjwb['dtjwb_catatan']
                ], null, 'A'.$rowExcel);

                $sheet->getStyle("B$rowExcel:J$rowExcel")
                      ->getAlignment()->setWrapText(true);

                $rowExcel++;
                $no++;
            }
        }
    }

    /* =====================================================
     * DOWNLOAD
     * ===================================================== */
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Audit_Mutu_'.$tahun.'.xls"');
    header('Cache-Control: max-age=0');

    $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $writer->save('php://output');
    exit;
}

/*
    public function download() {
        
        $ids = $this->input->post('ids');
        if(isset($ids)){
           
            // add a page
            $pdf = new \setasign\Fpdi\Fpdi('L','mm','A4');
            $i = 1;
            foreach($ids as $id){
                $audit = $this->mutu->getAuditById($id);
                // $formulir = $this->formulir->getFormulirById($audit['form_id']);
                //$dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
                //$jwb = $this->auditjawab->getAuditJawab($audit['audit_id'],$dtform[0]['dtform_id']);
                $tanggal = date("d", strtotime($audit['audit_update']));
                $bulan = date("m", strtotime($audit['audit_update']));
                $tahun = date("Y", strtotime($audit['audit_update']));

                $tgl_audit = $tanggal." ".konvbln($bulan)." ".$tahun;
                
                $pdf->AddPage();

                $pdf->SetFont('Arial', 'B', 22);
                $pdf->SetXY(0, 30);
                $pdf->Cell(0,6, $audit['form_nama'],0,1,'C');

                $gambar = FCPATH . 'filedata/logostikfinal.png';

                $pdf->Cell(0,120, '',0,1,'C');
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(0,6, 'PUSAT PENJAMINAN MUTU',0,1,'C');
                $pdf->Cell(0,6, 'SEKOLAH TINGGI ILMU KESEHATAN SITI KHADIJAH',0,1,'C');
                $pdf->Cell(0,6, 'TAHUN '.$tahun,0,1,'C');
                

                // Tentukan ukuran gambar
                $w = 60;  // lebar
                $h = 60;  // tinggi

                // Hitung posisi tengah
                $x = ($pdf->GetPageWidth() - $w) / 2;
                $y = ($pdf->GetPageHeight() - $h) / 2;

                // Tampilkan gambar
                $pdf->Image($gambar, $x, $y, $w, $h);

                $pdf->AddPage();
                $path = FCPATH . "filedata/mutufix.pdf";
                $pdf->setSourceFile($path);
                // import page 1
                $tplIdx = $pdf->importPage($i);
                $pdf->SetXY(0, 0);
                // use the imported page and place it at position 10,10 with a width of 100 mm
                $pdf->useTemplate($tplIdx, 0, 0, 297);

                $pdf->SetFont('Arial', 'B', 14);
                $pdf->SetXY(0, 49);
                $pdf->Cell(30.6,6, '',0,0,'C');
                $pdf->Cell(155.6,6, $audit['form_nama'],0,1,'C');
                $pdf->SetFont('Arial', '', 11);
                $pdf->SetXY(208, 19);
                $pdf->Write(6, $audit['form_kode']);

                $pdf->SetXY(208, 28);
                $tgl_form = date("d-m-Y", strtotime($audit['form_update']));
                $pdf->Write(6, $tgl_form);

                $pdf->SetXY(60, 61.6);
                $pdf->Write(6, $tgl_audit);

                $pdf->SetXY(60, 69);
                $pdf->Write(6, $audit['unit']);
                $pdf->SetXY(60, 76.4);
                $pdf->Write(6, $audit['auditor']);
                $pdf->SetXY(60, 83.8);
                $pdf->Write(6, $audit['auditee']);

                $pdf->SetFont('Arial', '', 10);
                $dtform = $this->dtform->getAllDtformByFormId($audit['form_id']);
                
                                
                                // Set lebar kolom
                $pdf->SetWidths([
                    11, 31.5, 49, 50, 37.45,
                    11.05, 10.05, 12.5, 12.5, 52.5
                ]);

                $pdf->SetAligns(['C','L','L','L','L','C','C','C','C','L']);

                $pdf->SetXY(8.7, 109.4); // posisi awal tabel

                $j = 1;

                foreach($dtform as $row){
                    $jwb = $this->auditjawab->getAuditJawabFix($audit['audit_id'],$row['dtform_id']);
                    if(isset($jwb) && count($jwb) > 0){
                    $detail = $this->auditjawabdetail->getAuditJawabDetail($jwb['jwb_id']);
                    if(isset($detail) && count($detail) > 0){
                    foreach($detail as $dtjwb):
                        if($dtjwb["dtjwb_temuan"] == "S"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                $dtjwb['dtjwb_temuan'],
                                "",
                                "",
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else if($dtjwb["dtjwb_temuan"] == "OB"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                $dtjwb['dtjwb_temuan'],
                                "",
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else if($dtjwb["dtjwb_temuan"] == "TS MINOR"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                "",
                                $dtjwb['dtjwb_temuan'],
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else if($dtjwb["dtjwb_temuan"] == "TS MAYOR"){
                            $dtjwb['dtjwb_temuan'] = "X";

                                $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                "",
                                "",
                                $dtjwb['dtjwb_temuan'],
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }else{
                            $pdf->Row([
                                $j,
                                strip_tags($jwb['jwb_tujuan']),
                                $dtjwb['dtjwb_referensi'],
                                $dtjwb['dtjwb_pertanyaan'],
                                $dtjwb['dtjwb_hasil'],
                                "",
                                "",
                                "",
                                "",
                                $dtjwb['dtjwb_catatan']
                            ]);
                        }
                        $j++;      
                    endforeach;
                    }
                }
                }
               // $i = $i + 2 + $j;

            }
            


            $pdf->Output('I', 'generated.pdf');
        }else{
            $this->session->set_flashdata('pesanerror', 'Tidak ada data yang dipilih!');
            redirect(base_url('admin/daftaraudit'));
        }
    }
*/
    public function index() {
            $this->data['content'] = 'daftaraudit/index';
            $this->data['title'] = 'Daftar Audit';
            $this->data['js'] = $this->load->get_js_files();
            $this->data['auditmenu'] = 'active';
            $this->data['audit'] = 'active';
            $this->data['totalterkirim'] = $this->mutu->totalTerkirim();
            $this->data['totalproses'] = $this->mutu->totalProses();
            $this->data['totalselesai'] = $this->mutu->totalSelesai();
            $this->data['totaldraft'] = $this->mutu->totalDraft();
            $this->data['listauditor'] = $this->akun->getAllAuditor();
            $this->data['listauditee'] = $this->akun->getAllAuditee();
            $this->data['formulir'] = $this->formulir->getAllFormulir();
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
    }

    public function detail($id) {
        if(isset($id)){
            $this->data['content'] = 'daftaraudit/detail';
            $this->data['title'] = 'Daftar Audit';
            $this->data['audit_id'] = $id;
            $this->data['audit'] = 'active';//auditmenu
            $this->data['auditmenu'] = 'active';
            $this->data['result'] = $this->mutu->getAuditById($id);
            $this->data['js'] = $this->load->get_js_files();
            $this->data['audit'] = 'active';
            $this->data['pesanerror'] = $this->session->flashdata('pesanerror');
            $this->data['pesanberhasil'] = $this->session->flashdata('pesanberhasil');
            $this->template($this->data, $this->module); 
        }
    }

   public function tambah() {
        $form_id = $this->input->post('form_id');
        if($form_id){
                $data = array();
                $data['form_id'] = $form_id;
                $data['auditor_id'] = $this->input->post('auditor');
                $data['auditee_id']  = $this->input->post('auditee');

                $akunauditee = $this->akun->getAkunById($data['auditee_id']);
                $data['auditee'] = $akunauditee['nama'];
                $data['unit'] = $akunauditee['unitkerja'];

                $akunauditor = $this->akun->getAkunById($data['auditor_id']);
                $data['auditor'] = $akunauditor['nama'];

                if ($this->mutu->add($data)) {
                    $query = array("status" => true, "pesan" => "Berhasil");
                } else {
                    $query = array("status" => false, "pesan" => "Gagal");
                }

                header('Access-Control-Allow-Origin: *');
                header('Content-Type: application/json');
                echo json_encode($query);

        }else{
            $query = array("status" => false, "pesan" => "Silahkan pilih form audit");
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        } 
    }

    public function getauditById($id) {
        if(isset($id)){
            $query = $this->mutu->getauditById($id);
            $query['status'] = true;
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query);
        }else{
            $query = array("status"=>false);
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            echo json_encode($query); 
        }
    }

    public function jawab() {
        $data = array();
        $data['dtform_id'] = $this->input->post('dtform_id');
        $data['audit_id'] = $this->input->post('audit_id');
        $data['jwb_jawaban'] = $this->input->post('jwb_jawaban');
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

    public function hapus() {
        $id = $this->input->post('id');
        $status = $this->mutu->hapus($id);
        $query = array("status" => $status);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($query);
    }

    public function listmutu() {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->mutu->get_datatables($post['length'], $post['start'], $post['search'], $post['order']);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = '<input type="checkbox" class="pilih" value="'.$field->audit_id.'"> '.$no;
            $row[] = $field->form_nama;
            $row[] = $field->auditor;
            $row[] = $field->auditee;
            $row[] = $field->unit;
            
            //$row[] = $field->audit_status;
            if($field->audit_status == "DRAFT"){
                $row[] = '<button class="detail btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="DETAIL" id=' . $field->audit_id.'><i class="icon md-book" aria-hidden="true"></i></button> <button class="delete btn btn-sm btn-icon btn-danger"
            data-toggle="tooltip" data-original-title="DELETE" id=' . $field->audit_id.'><i class="icon md-delete" aria-hidden="true"></i></button>';
                $row[] = '<button class="btn btn-primary btn-xs waves-effect waves-classic"
            data-toggle="tooltip" data-original-title="DRAFT">Draft</button>';
            }else if($field->audit_status == "TERKIRIM"){           
                $row[] = '<button class="detail btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="DETAIL" id=' . $field->audit_id.'><i class="icon md-book" aria-hidden="true"></i></button>';                             
                $row[] = '<button class="btn btn-danger btn-xs waves-effect waves-classic"
            data-toggle="tooltip" data-original-title="DRAFT">Terkirim</button>';
            }else if($field->audit_status == "PROSES"){
                $row[] = '<button class="detail btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="DETAIL" id=' . $field->audit_id.'><i class="icon md-book" aria-hidden="true"></i></button>';
                $row[] = '<button type="button" class="btn btn-warning btn-xs waves-effect waves-classic"><i class="icon md-home" aria-hidden="true"></i>Diproses</button>';
            }else if($field->audit_status == "SELESAI"){
                $row[] = '<button class="detail btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="DETAIL" id=' . $field->audit_id.'><i class="icon md-book" aria-hidden="true"></i></button> <button class="download btn btn-sm btn-icon btn-success"
            data-toggle="tooltip" data-original-title="DELETE" id=' . $field->audit_id.'><i class="icon md-download" aria-hidden="true"></i></button>';
                $row[] = '<button type="button" class="selesai btn btn-success btn-xs waves-effect waves-classic"><i class="icon md-download" aria-hidden="true"></i>Selesai</button>';
            }
            
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->mutu->count_all(),
            "recordsFiltered" => $this->mutu->count_filtered($post['search'], $post['order']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    


    public function listpertanyaan($id=null) {
        $post = array();
        $post['search'] = $this->input->post('search');
        $post['order'] = $this->input->post('order');
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $post['draw'] = $this->input->post('draw');


        $list = $this->auditjawab->get_datatables($post['length'], $post['start'], $post['search'], $post['order'],$id);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->dtform_pertanyaan."<br/>".$field->dtform_lingkup;
            //$jawaban = $this->truncate_words($field->jwb_jawaban);
           // $row[] = $field->jwb_jawaban;
           if(isset($field->jwb_jawaban)){
                $row[] = ' <button type="button" class="delik btn btn-warning btn-xs waves-effect waves-classic" dtform_id=' . $field->dtform_id.' audit_id=' . $field->audit_id.'><i class="icon md-edit" aria-hidden="true"></i>Lihat Hasil dan Delik</button>';
           }else{
                $row[] = '';
           }
           
            $data[] = $row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->auditjawab->count_all($id),
            "recordsFiltered" => $this->auditjawab->count_filtered($post['search'], $post['order'],$id),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


}
