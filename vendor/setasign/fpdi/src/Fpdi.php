<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfNull;

/**
 * Class Fpdi
 *
 * This class let you import pages of existing PDF documents into a reusable structure for FPDF.
 */
class Fpdi extends FpdfTpl
{
    use FpdiTrait;
    use FpdfTrait;

    const VERSION = '2.6.4';

    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    // function Row($data)
    // {
    //     $nb = 0;
    //     for($i=0;$i<count($data);$i++)
    //         $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    //     $h = 6 * $nb;

    //     // Check page break
    //     $this->CheckPageBreak($h);

    //     // Save starting X & Y
    //     $xStart = $this->GetX();
    //     $yStart = $this->GetY();

    //     for($i = 0; $i < count($data); $i++)
    //     {
    //         $w = $this->widths[$i];
    //         $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

    //         $x = $this->GetX();
    //         $y = $this->GetY();

    //         // Draw cell border
    //         $this->Rect($x, $y, $w, $h);

    //         // Output text
    //         $this->MultiCell($w, 6, $data[$i], 0, $a);

    //         // Move cursor back to top-right of the cell
    //         $this->SetXY($x + $w, $y);
    //     }

    //     // Move to next row
    //     $this->SetXY($xStart, $yStart + $h);
    // }

    function Row($data)
{
    // Hitung tinggi baris
    $nb = 0;
    for ($i = 0; $i < count($data); $i++) {
        $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    }
    $h = 6 * $nb;

    // ====== KUNCI UTAMA ======
    // Jika TIDAK MUAT → PINDAH HALAMAN SEBELUM CETAK ROW
    if ($this->GetY() + $h > $this->PageBreakTrigger) {
        $this->AddPage($this->CurOrientation);
        // posisi X formulir Anda
        $this->SetX(8.7);
    }

    // Simpan posisi awal
    $xStart = $this->GetX();
    $yStart = $this->GetY();

    // ===== MATIKAN AUTO PAGE BREAK MULTICELL =====
    $autoPageBreak = $this->AutoPageBreak;
    $this->SetAutoPageBreak(false);

    // Cetak semua kolom
    for ($i = 0; $i < count($data); $i++) {

        $w = $this->widths[$i];
        $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

        $x = $this->GetX();
        $y = $this->GetY();

        // Border FULL tinggi baris
        $this->Rect($x, $y, $w, $h);

        // Isi cell
        $this->MultiCell($w, 6, $data[$i], 0, $a);

        // Kembali ke atas kolom
        $this->SetXY($x + $w, $y);
    }

    // Aktifkan kembali auto page break
    $this->SetAutoPageBreak($autoPageBreak);

    // Turun ke baris berikutnya
    $this->SetXY($xStart, $yStart + $h);
}



    function CheckPageBreak($h)
    {
        // If the next row goes beyond the page, create a new one
        if ($this->GetY() + $h > $this->PageBreakTrigger) {

            // Add a new page
            $this->AddPage($this->CurOrientation);

            // If you want the table to always align at X=8.7, enable the line below:
            // $this->SetX(8.7);
        }
    }


    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;

        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if($nb > 0 && $s[$nb-1] == "\n")
            $nb--;

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;

        while($i < $nb){
            $c = $s[$i];

            if($c == "\n"){
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }

            if($c == ' ')
                $sep = $i;

            $l += $cw[$c];

            if($l > $wmax){
                if($sep == -1){
                    if($i == $j)
                        $i++;
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }

        return $nl;
    }

}
