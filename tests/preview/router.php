<?php
/**
 * Router untuk `php -S` saat menguji aplikasi secara lokal (bukan bagian
 * aplikasi produksi).
 *
 * - Berkas statis di docroot dilayani langsung oleh server bawaan PHP.
 * - Rute CodeIgniter lain diarahkan ke index.php (base_url tanpa index.php,
 *   uri_protocol REQUEST_URI).
 *
 * Contoh:
 *   php -S 0.0.0.0:8090 -t /home/user/preview/app tests/preview/router.php
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$docroot = $_SERVER['DOCUMENT_ROOT'];
$berkas = rtrim($docroot, '/') . $path;

if ($path !== '/' && is_file($berkas)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = rtrim($docroot, '/') . '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require rtrim($docroot, '/') . '/index.php';
