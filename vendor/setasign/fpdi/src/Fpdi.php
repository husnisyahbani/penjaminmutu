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

    /**
     * FPDI version
     *
     * @string
     */
    const VERSION = '2.6.4';

    function NbLines($w, $txt)
{
    global $pdf;
    $cw = &$pdf->CurrentFont['cw'];
    if($w==0)
        $w = $pdf->w - $pdf->rMargin - $pdf->x;
    $wmax = ($w - 2*$pdf->cMargin) * 1000 / $pdf->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
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
            }else
                $i = $sep + 1;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
        }else
            $i++;
    }
    return $nl;
}

}
