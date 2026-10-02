<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Bantuan untuk tabel lingkup audit (mutu_lingkup).
 *
 * Sebelumnya lingkup pertanyaan disimpan sebagai satu kolom teks (HTML) pada
 * mutu_detailform.dtform_lingkup. Sekarang satu pertanyaan dapat memiliki
 * banyak butir lingkup, sehingga isi kolom lama perlu dipecah menjadi
 * baris-baris mutu_lingkup.
 */

/**
 * Pecah isi kolom dtform_lingkup (HTML) menjadi daftar butir lingkup.
 *
 * Aturan:
 *   - <li> menjadi satu butir per item,
 *   - <p>/<div>/<br>/heading di luar daftar juga menjadi butir,
 *   - teks kosong atau baris judul yang diakhiri ":" (mis. "Dokumen
 *     Pendukung :") dilewati karena bukan butir penilaian,
 *   - butir yang sama persis tidak diulang.
 *
 * @param  string $html isi kolom dtform_lingkup
 * @return array  daftar teks butir lingkup (sudah bersih, siap simpan)
 */
function lingkup_parse($html) {
    if (!is_string($html) || trim($html) === '') {
        return array();
    }

    // <br> sama dengan pemisah baris.
    $teks = preg_replace('/<br\s*\/?>/i', "\n", $html);

    // Potong pada akhir setiap blok, urutan tetap seperti di dokumen.
    $potongan = preg_split(
        '/(?=<li\b)|(?<=<\/li>)|(?<=<\/p>)|(?<=<\/div>)|(?<=<\/h[1-6]>)|(?<=<\/ul>)|(?<=<\/ol>)|(?<=<\/blockquote>)|(?<=<\/table>)/i',
        $teks
    );

    $butir = array();
    $sudah = array();

    foreach ($potongan as $bagian) {
        // Satu potongan dapat berisi beberapa <li> (mis. <ul><li>a</li><li>b</li>).
        $kandidat = array();
        if (preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $bagian, $cocok)) {
            foreach ($cocok[1] as $isi) {
                $kandidat[] = $isi;
            }
            // Sisa teks di luar <li> pada potongan yang sama (mis. pengantar).
            $sisa = trim(preg_replace('/<li\b[^>]*>.*?<\/li>/is', ' ', $bagian));
            if ($sisa !== '' && strip_tags($sisa) !== '') {
                $kandidat[] = $sisa;
            }
        } else {
            $kandidat[] = $bagian;
        }

        foreach ($kandidat as $isi) {
            $bersih = lingkup_bersihkan($isi);
            if ($bersih === '') {
                continue;
            }

            // Judul/label pendek yang diakhiri ":" bukan butir penilaian.
            if (lingkup_judul($bersih)) {
                continue;
            }

            $kunci = lingkup_normal($bersih);
            if (isset($sudah[$kunci])) {
                continue;
            }
            $sudah[$kunci] = TRUE;

            $butir[] = $bersih;
        }
    }

    return $butir;
}

/**
 * Bersihkan potongan HTML menjadi teks biasa: tag dibuang, entitas dibuka,
 * spasi & baris kosong dirapikan.
 */
function lingkup_bersihkan($html) {
    if ($html === NULL) {
        return '';
    }

    $teks = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $html);
    $teks = preg_replace('/(<\/p>|<\/div>|<\/li>|<\/h[1-6]>|<\/tr>)/i', ' ', $teks);
    $teks = str_replace(array('&nbsp;', '&#160;'), ' ', $teks);
    $teks = strip_tags($teks);
    $teks = html_entity_decode($teks, ENT_QUOTES, 'UTF-8');
    $teks = preg_replace('/\s+/u', ' ', $teks);
    return trim($teks);
}

/**
 * Apakah teks ini hanya judul/label (mis. "Dokumen Pendukung :")?
 */
function lingkup_judul($teks) {
    $t = trim($teks);
    if ($t === '') {
        return TRUE;
    }
    if (strpos($t, '?') !== FALSE) {
        return FALSE;
    }
    // Diakhiri titik dua dan pendek -> label, bukan butir.
    return (mb_substr_potong($t, -1) === ':' && hitung_potong($t) <= 80);
}

/**
 * Bentuk normal untuk membandingkan dua butir (huruf kecil, tanpa tanda baca,
 * spasi tunggal).
 */
function lingkup_normal($teks) {
    $t = strtolower(lingkup_bersihkan($teks));
    $t = preg_replace('/[^a-z0-9]+/', ' ', $t);
    return trim(preg_replace('/\s+/', ' ', $t));
}

/* --- pembungkus kecil supaya tidak bergantung pada mbstring --- */

function mb_substr_potong($teks, $mulai) {
    if (function_exists('mb_substr')) {
        return mb_substr($teks, $mulai, NULL, 'UTF-8');
    }
    return substr($teks, $mulai);
}

function hitung_potong($teks) {
    if (function_exists('mb_strlen')) {
        return mb_strlen($teks, 'UTF-8');
    }
    return strlen($teks);
}

/**
 * Ubah isi (HTML atau teks) menjadi teks biasa yang tetap mempertahankan
 * baris baru. Dipakai kolom "Rencana Koreksi" pada halaman PTK dan daftar
 * butir halaman delik: rencana koreksi lama tersimpan sebagai HTML, sedangkan
 * yang baru berupa teks polos dengan baris baru.
 *
 * @param  string $nilai isi HTML/teks
 * @return string teks polos
 */
function lingkup_teks_baris($nilai) {
    $teks = (string) $nilai;

    if (strpos($teks, '<') !== FALSE) {
        // Tag blok menjadi baris baru supaya daftar tetap terbaca.
        $teks = preg_replace('/<br\s*\/?>/i', "\n", $teks);
        $teks = preg_replace('/<li\b[^>]*>/i', '- ', $teks);
        $teks = preg_replace('/<\/(p|div|li|ul|ol|h[1-6]|tr)>/i', "\n", $teks);
        $teks = strip_tags($teks);
    }

    $teks = html_entity_decode($teks, ENT_QUOTES, 'UTF-8');
    $teks = str_replace(array("\r\n", "\r"), "\n", $teks);
    $teks = preg_replace('/[ \t]+/', ' ', $teks);
    $teks = preg_replace('/ ?\n ?/', "\n", $teks);
    $teks = preg_replace('/\n{3,}/', "\n\n", $teks);

    return trim($teks);
}
