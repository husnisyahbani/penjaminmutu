<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('nominal')) {
	function nominal($angka)
	{
		$jd = number_format($angka, 0, ',', '.');
		return $jd;
	}
}

if (!function_exists('nominal2')) {
	function nominal2($angka)
	{
		$jd = number_format($angka, 2, ',', '.');
		return $jd;
	}
}