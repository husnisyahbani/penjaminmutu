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