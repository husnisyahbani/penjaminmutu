<?php

class Latihan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module = 'umum';
        $this->load->library('pdf');
    }

    public function index() {
        
        $pdf = new FPDF('p', 'mm', 'A4');
        // membuat halaman baru
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        // Move to 8 cm to the right
        $pdf->Cell(80);
        // Centered text in a framed 20*10 mm cell and line break
        $pdf->Cell(20, 10, 'Title', 1, 1, 'C');
        //$this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('result.pdf', 'D');
    }

}
