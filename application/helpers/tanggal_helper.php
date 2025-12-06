<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('konvtgl')) {
	function konvtgl($var = '')
	{
		//tgl d/m/y -> y/m/d
		$spasi = explode(" ", $var);

		$cekpemisah = strpos($spasi[0], "/");
		if ($cekpemisah) {
			$sekarang = explode("/", $spasi[0]);
		} else {
			$sekarang = explode("-", $spasi[0]);
		}
		if (intval($var) !== 0) {
			return $sekarang[2] . "-" . $sekarang[0] . "-" . $sekarang[1];
		} else {
			return "0000-00-00";
		}
	}
}

if (!function_exists('truncate_words')) {
function truncate_words(string $text, int $maxWords = 200, string $ellipsis = '...'): string {
    // bersihkan whitespace berlebih dan hilangkan tag HTML jika perlu
    $clean = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
    if ($clean === '') return $clean;

    $words = preg_split('/\s+/', $clean);
    if (count($words) <= $maxWords) {
        return $clean;
    }

    $truncated = array_slice($words, 0, $maxWords);
    return implode(' ', $truncated) . $ellipsis;
}
}

if (!function_exists('konvbln')) {
	function konvbln($bulan)
	{
		if($bulan == 1){
            return "Januari";
        }else if($bulan == 2){
            return "Februari";
        }else if($bulan == 3){
            return "Maret";
        }else if($bulan == 4){
            return "April";
        }else if($bulan == 5){
            return "Mei";
        }else if($bulan == 6){
            return "Juni";
        }else if($bulan == 7){
            return "Juli";
        }else if($bulan == 8){
            return "Agustus";
        }else if($bulan == 9){
            return "September";
        }else if($bulan == 10){
            return "Oktober";
        }else if($bulan == 11){
            return "November";
        }else if($bulan == 12){
            return "Desember";
        }
	}
}