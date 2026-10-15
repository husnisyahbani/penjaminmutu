<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper tampilan halaman depan (homepage) dan halaman turunannya
 * (login & sub halaman SPMI) yang memakai tema yang sama.
 *
 * Catatan: homepage.php juga mendefinisikan fungsi ini secara inline
 * dengan guard function_exists(), sehingga aman dipanggil dari keduanya.
 */

if (!function_exists('hp_shade')) {
    /**
     * Terangkan (delta > 0) atau gelapkan (delta < 0) sebuah warna hex.
     *
     * @param  string $hex  warna hex (#rgb / #rrggbb)
     * @param  float  $delta -1 s/d 1
     * @return string
     */
    function hp_shade($hex, $delta)
    {
        $hex = ltrim(trim((string) $hex), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (!preg_match('/^[0-9a-f]{6}$/i', $hex)) {
            $hex = '0f766e';
        }

        $out = '#';

        for ($i = 0; $i < 3; $i++) {
            $c = hexdec(substr($hex, $i * 2, 2));
            $c = max(0, min(255, (int) round($c + ($c * $delta))));
            $out .= str_pad(dechex($c), 2, '0', STR_PAD_LEFT);
        }

        return $out;
    }
}

if (!function_exists('hp_potong')) {
    /**
     * Potong teks berdasarkan jumlah kata.
     *
     * @param  string $teks
     * @param  int    $jumlah
     * @return string
     */
    function hp_potong($teks, $jumlah = 22)
    {
        $teks = trim(preg_replace('/\s+/', ' ', strip_tags((string) $teks)));

        if ($teks === '') {
            return '';
        }

        $kata = explode(' ', $teks);

        if (count($kata) <= $jumlah) {
            return $teks;
        }

        return implode(' ', array_slice($kata, 0, $jumlah)) . ' ...';
    }
}
