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