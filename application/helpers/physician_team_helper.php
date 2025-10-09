<?php if (!defined('BASEPATH')) exit('No direct script access allowed');




/* 

BPJS TINDAKAN DAN VISIT BEDAH

*/

if (!function_exists('visit_bpjs_ok')) {
    function visit_bpjs_ok($rupiah,$jumlah,$codetim)
    {
        $rupiah_visit = '';
        switch ($codetim) {
                /* DPJP UTAMA */
            case 'X0167^001':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* DPJP Konsul */
            case 'X0167^002':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* DPJP Intensive */
            case 'X0167^003':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* General Practice ( Dr Umum ) */
            case 'X0167^004':
                $rupiah_visit = ($rupiah*0.1)*$jumlah;
                break;
                /* DPJP Anesthesia */
            case 'X0167^005':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* DPJP Anak */
            case 'X0167^006':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* DPJP Konsul Intra OPS */
            case 'X0167^008':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* DPJP Raber */
            default: 
            $rupiah_visit = 0;            
        }
        return $rupiah_visit;
    }
}

if (!function_exists('tindakan_bpjs_ok')) {
    function tindakan_bpjs_ok($total_rupiah,$rupiah_nonbedah,$codetim)
    {
        $rupiah_tindakan = '';
        switch ($codetim) {
                /* DPJP UTAMA */
            case 'X0167^001':
                $rupiah_tindakan = $total_rupiah*0.26;
                break;
                /* DPJP Konsul */
            case 'X0167^002':
                $rupiah_tindakan = $rupiah_nonbedah*0.15;
                break;
                /* DPJP Intensive */
            case 'X0167^003':
                $rupiah_tindakan = $rupiah_nonbedah*0.15;
                break;
                /* General Practice ( Dr Umum ) */
            case 'X0167^004':
                $rupiah_tindakan = $rupiah_nonbedah*0.20;
                break;
                /* DPJP Anesthesia */
            case 'X0167^005':
                $rupiah_tindakan = $total_rupiah*0.20;
                break;
                /* DPJP Anak */
            case 'X0167^006':
                $rupiah_tindakan = $total_rupiah*0.10;
                break;
                /* DPJP Konsul Join Intra OPS */
            case 'X0167^007':
                $rupiah_tindakan = $total_rupiah*0.33;
                break;
                /* DPJP Konsul Intra OPS */
            case 'X0167^008':
                $rupiah_tindakan = $total_rupiah*0.15;
                break;
                /* DPJP Raber */
            default: 
                $rupiah_tindakan = 0;       
        }
        return $rupiah_tindakan;
    }
}

/* 

BPJS TINDAKAN DAN VISIT NON BEDAH

*/

if (!function_exists('tindakan_bpjs_non_op')) {
    function tindakan_bpjs_non_op($total_rupiah,$rupiah_nonbedah,$codetim)
    {
        $rupiah_tindakan = '';
        switch ($codetim) {
                /* DPJP UTAMA */
            case 'X0167^001':
                $rupiah_tindakan = $total_rupiah*0.26;
                break;
                /* DPJP Konsul */
            case 'X0167^002':
                $rupiah_tindakan = $total_rupiah*0.15;
                break;
                /* DPJP Intensive */
            case 'X0167^003':
                $rupiah_tindakan = $rupiah_nonbedah*0.15;
                break;
                /* General Practice ( Dr Umum ) */
            case 'X0167^004':
                $rupiah_tindakan = $rupiah_nonbedah*0.20;
                break;
                /* DPJP Raber */
            case 'X0167^009':
                $rupiah_tindakan = $total_rupiah*0.15;
                break;
            default: 
                $rupiah_tindakan = 0;       
        }
        return $rupiah_tindakan;
    }
}


if (!function_exists('visit_bpjs_non_op')) {
    function visit_bpjs_non_op($rupiah,$jumlah,$codetim)
    {
        $rupiah_visit = '';
        switch ($codetim) {
                /* DPJP UTAMA */
            case 'X0167^001':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* DPJP Konsul */
            case 'X0167^002':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* DPJP Intensive */
            case 'X0167^003':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
                /* General Practice ( Dr Umum ) */
            case 'X0167^004':
                $rupiah_visit = ($rupiah*0.5)*$jumlah;
                break;
                 /* DPJP Raber */
            case 'X0167^009':
                $rupiah_visit = ($rupiah*0.25)*$jumlah;
                break;
            default: 
            $rupiah_visit = 0;            
        }
        return $rupiah_visit;
    }
}

/*

UMUM BEDAH 

*/ 

if (!function_exists('visit_umum_operatif')) {
    function visit_umum_operatif($code,$rupiah,$persen)
    {
        $kode = '';
        switch ($code) {
                /* DPJP UTAMA */
            case 'X0167^001':
                $kode = "0.26";
                break;
                /* DPJP Konsul */
            case 'X0167^002':
                $kode = "0.2";
                break;
                /* DPJP Intensive */
            case 'X0167^003':
                $kode = "0.26";
                break;
                /* General Practice ( Dr Umum ) */
            case 'X0167^004':
                $kode = "0.2";
                break;
                /* DPJP Anesthesia */
            case 'X0167^005':
                $kode = "0.2";
                break;
                /* DPJP Anak */
            case 'X0167^006':
                $kode = "0.1";
                break;
                /* DPJP Konsul Join Intra OPS */
            case 'X0167^007':
                $kode = "0.33";
                break;
                /* DPJP Konsul Intra OPS */
            case 'X0167^008':
                $kode = "0.26";
                break;
                /* DPJP Raber */
            case 'X0167^009':
                $kode = "0";
                break;
        }
        return $kode;

    }
}


if (!function_exists('umum_ok_tindakan')) {
    function umum_ok_tindakan($var)
    {
        $kode = '';
        switch ($var) {
                /* DPJP UTAMA */
            case 'X0167^001':
                $kode = "0.26";
                break;
                /* DPJP Konsul */
            case 'X0167^002':
                $kode = "0.2";
                break;
                /* DPJP Intensive */
            case 'X0167^003':
                $kode = "0.26";
                break;
                /* General Practice ( Dr Umum ) */
            case 'X0167^004':
                $kode = "0.2";
                break;
                /* DPJP Anesthesia */
            case 'X0167^005':
                $kode = "0.2";
                break;
                /* DPJP Anak */
            case 'X0167^006':
                $kode = "0.1";
                break;
                /* DPJP Konsul Join Intra OPS */
            case 'X0167^007':
                $kode = "0.33";
                break;
                /* DPJP Konsul Intra OPS */
            case 'X0167^008':
                $kode = "0.26";
                break;
                /* DPJP Raber */
            case 'X0167^009':
                $kode = "0";
                break;
        }
        return $kode;
    }
}

/*

UMUM NON BEDAH 

*/ 

if (!function_exists('umum_non_ok')) {
    function umum_non_ok($var)
    {
        $kode = '';
        switch ($var) {
                /* DPJP UTAMA */
            case 'X0167^001':
                $kode = "0.26";
                break;
                /* DPJP Konsul */
            case 'X0167^002':
                $kode = "0.2";
                break;
                /* DPJP Intensive */
            case 'X0167^003':
                $kode = "0.26";
                break;
                /* General Practice ( Dr Umum ) */
            case 'X0167^004':
                $kode = "0.2";
                break;
                /* DPJP Raber */
            case 'X0167^009':
                $kode = "0.26";
                break;
        }
        return $kode;
    }
}



if (!function_exists('umum_rj_tindakan')) {
    function umum_rj_tindakan($var)
    {
        $kode = '';
        switch ($var) {
                    /* DPJP UTAMA */
                    case 'X0167^001':
                        $kode = "0.26";
                        break;
                        /* DPJP Konsul */
                    case 'X0167^002':
                        $kode = "0.26";
                        break;
                        /* General Practice ( Dr Umum ) */
                    case 'X0167^004':
                        $kode = "0.2";
                        break;
        }
        return $kode;
    }
}



/*

PENUNJANG UMUM

*/ 

if (!function_exists('umum_penunjang')) {
    function umum_penunjang($var)
    {
        $kode = '';
        switch ($var) {
                    /* DPJP UTAMA */
                    case 'X0167^001':
                        $kode = "0.26";
                        break;
                        /* DPJP Konsul */
                    case 'X0167^002':
                        $kode = "0.26";
                        break;
                        /* General Practice ( Dr Umum ) */
                    case 'X0167^004':
                        $kode = "0.2";
                        break;
        }
        return $kode;
    }
}