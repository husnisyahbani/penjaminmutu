<?php

class Hasil extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->model('IzinpenelitianModel', 'izinpenelitian');
        $this->load->model('SuratModel', 'surat');
    }

    public function index() {
        $this->data['content'] = 'hasil';
        $this->data['title'] = 'Permohanan Izin Penelitian';
        $id = $this->input->get('id');
        $hasil = $this->izinpenelitian->get_izin($id);
        if(!empty($hasil))
        $this->data['hasil'] = $hasil[0]['ip_status'];
        $this->template($this->data, $this->module);
    }
    
    public function noantrian($id) {
        $this->data['content'] = 'hasil';
        $this->data['hasil'] = sprintf('%03d',$id);
        $this->template($this->data, $this->module);
    }

    public function download() {
        $id = $this->input->get('id');
        $hasil = $this->surat->get_surat($id);

        $this->load->library('pdf');
        $pdf = new FPDF('p', 'mm', 'A4');
        // membuat halaman baru
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetAutoPageBreak(true , 15);
        $lineheight = 6;
        
        $pdf->Image('assets/assets/images/header.png', 10, 10, 185);
        $pdf->setXY(20,48);
        $ip_tanggal = tgl_indo($hasil[0]['ip_tanggal']);
        $pdf->Cell(100,$lineheight, '', 0, 0, 'R');
        $pdf->Cell(70, $lineheight, 'Palembang, '.$ip_tanggal, 0, 1, 'L');
        $pdf->setXY(20,60);
        $pdf->Cell(20, $lineheight, 'Nomor', 0, 0, 'L');
        $pdf->Cell(80, $lineheight, ':'.$hasil[0]['srt_no'], 0, 0, 'L');
        $pdf->Cell(50, $lineheight, 'Kepada Yth.', 0, 1, 'L');
        
        $pdf->Cell(10, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(20, $lineheight, 'Lampiran', 0, 0, 'L');
        $pdf->Cell(100, $lineheight, ': -', 0, 1, 'L');
        
        $pdf->Cell(10, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(20, $lineheight, 'Perihal', 0, 0, 'L');
        $pdf->Cell(100, $lineheight, ': Izin Penelitian', 0, 1, 'L');
        
        $pdf->Cell(10, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(20, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(10, $lineheight, '  a.n.', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(90, $lineheight, $hasil[0]['ip_nama'], 0, 1, 'L');
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->setXY(120,66);
        if(!is_null($hasil[0]['ip_fakultas'])){
            $pdf->MultiCell(80,$lineheight,'Dekan Fakultas '.$hasil[0]['ip_fakultas']." ".$hasil[0]['ip_asalpt'],0,'L');
        }else{
            $pdf->MultiCell(80,$lineheight,'Ketua '.$hasil[0]['ip_asalpt'],0,'L');
        }
        
        
        $pdf->setXY(40,90);
        if(!is_null($hasil[0]['ip_fakultas'])){
            $pdf->MultiCell(150,$lineheight,'          Menindaklanjuti Surat Dekan Fakultas '.$hasil[0]['ip_fakultas'].' '.$hasil[0]['ip_asalpt'].' Nomor : '.$hasil[0]['ip_nosurat'].' Pada Tanggal : '.tgl_indo($hasil[0]['ip_tglsurat']).' perihal Izin Penelitian. Sehubungan dengan hal tersebut, kami memberikan izin kepada :',0,'L');
        }else{
            $pdf->MultiCell(150,$lineheight,'          Menindaklanjuti Surat Ketua '.$hasil[0]['ip_asalpt'].' Nomor : '.$hasil[0]['ip_nosurat'].' Pada Tanggal : '.tgl_indo($hasil[0]['ip_tglsurat']).' perihal Izin Penelitian. Sehubungan dengan hal tersebut, kami memberikan izin kepada :',0,'L');
        }
        

        
        
       // $pdf->setXY(40,115);
        $pdf->Cell(30, $lineheight, '', 0, 1, 'L');
        $pdf->Cell(40, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(40, $lineheight, 'Nama', 0, 0, 'L');
        $pdf->Cell(5, $lineheight, ':', 0, 0, 'C');
        $pdf->Cell(95, $lineheight, $hasil[0]['ip_nama'], 0, 1, 'L');
        
        $pdf->Cell(40, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(40, $lineheight, 'NIM', 0, 0, 'L');
        $pdf->Cell(5, $lineheight, ':', 0, 0, 'C');
        $pdf->Cell(95, $lineheight, $hasil[0]['ip_npm'], 0, 1, 'L');
        
        $pdf->Cell(40, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(40, $lineheight, 'Program Studi', 0, 0, 'L');
        $pdf->Cell(5, $lineheight, ':', 0, 0, 'C');
        $pdf->MultiCell(95, $lineheight,$hasil[0]['ip_prodi'].' '.$hasil[0]['ip_asalpt'], 0, 'L');
        //$pdf->Cell(95, $lineheight, $hasil[0]['ip_prodi'].' '.$hasil[0]['ip_asalpt'], 0, 1, 'L');
        
        $pdf->Cell(40, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(40, $lineheight, 'Judul', 0, 0, 'L');
        $pdf->Cell(5, $lineheight, ':', 0, 0, 'C');
        $pdf->MultiCell(95, $lineheight,$hasil[0]['ip_judul'] , 0, 'L');
        
        $pdf->Cell(30, $lineheight, '', 0, 1, 'L');
        $pdf->Cell(30, $lineheight, '', 0, 0, 'L');
        $pdf->MultiCell(150,$lineheight,'          Untuk melakukan penelitian di '.$hasil[0]['ip_lokasi'].' pada tanggal, '.tgl_indo($hasil[0]['ip_mulai']).' s.d. '.tgl_indo($hasil[0]['ip_selesai']).' dan untuk selanjutnya dapat langsung ber koordinasi dengan Kepala '.$hasil[0]['ip_lokasi'].'.',0,'L');
        
        $pdf->Cell(30, $lineheight, '', 0, 1, 'L');
        $pdf->Cell(30, $lineheight, '', 0, 0, 'L');
        $pdf->MultiCell(150,$lineheight,'          Demikian atas perhatian Saudara, terimakasih',0,'L');
        
        $pdf->Cell(80, $lineheight, '', 0, 1, 'L');
        $pdf->Cell(100, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(10, $lineheight, 'a.n', 0, 0, 'L');
        $pdf->MultiCell(80,$lineheight,$hasil[0]['tt_jabatan'].",",0,'L');
        
        $tinggi = $pdf->GetY();
        
        
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, $lineheight*4, '', 0, 1, 'L');
        $pdf->Cell(110, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(80,$lineheight,$hasil[0]['tt_nama'],0,1,'L');
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(110, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(80,$lineheight,$hasil[0]['tt_pangkat'],0,1,'L');
        
        $pdf->Cell(110, $lineheight, '', 0, 0, 'L');
        $pdf->Cell(80,$lineheight,'NIP '.$hasil[0]['tt_nip'],0,1,'L');
        
        if(!empty($hasil[0]['tt_qr']))
        $pdf->Image(base_url().$hasil[0]['tt_qr'], 70, $tinggi, 30,30);
        
        if(!empty($hasil[0]['tt_file']))
        $pdf->Image(base_url().$hasil[0]['tt_file'], 98, $tinggi-12, 60,50);
        
        $pdf->setXY(40,263);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(80, $lineheight, '', 0, 1, 'L');
        $pdf->Cell(80,$lineheight/2,'Tembusan Yth:',0,1,'L');
        $pdf->SetFont('Arial', '', 8);
        //1. Kepala Dinas Pendidikan Provinsi Sumatera Selatan
        
        $pdf->Cell(80,$lineheight/2,'1. Kepala Dinas Pendidikan Provinsi Sumatera Selatan',0,1,'L');
        $pdf->Cell(80,$lineheight/2,'2. Kepala '.$hasil[0]['ip_lokasi'],0,1,'L');
        $pdf->Cell(80,$lineheight/2,'3. Yang Bersangkutan',0,1,'L');
        $pdf->Output();
    }

}
