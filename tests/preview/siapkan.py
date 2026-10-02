#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Skrip bantu pengujian lokal (tidak dipakai aplikasi produksi).

Membangun lingkungan uji dari nol:

  /home/user/preview/app          salinan aplikasi (docroot `php -S`)
  /home/user/preview/mutu.sqlite  database SQLite hasil impor database/05_09_2025.sql

Lingkungan sengaja dibuat "mirip database nyata":
  - tanpa tabel mutu_lingkup dan tanpa kolom auditjawab.lingkup_id
  - mutu_lampiran hanya punya kolom lingkup_id (belum dtjwb_id / jwb_id)
  - auditjawabdetail tanpa dtjwb_jawaban / dtjwb_koreksi
  - detailform tanpa dtform_urut
  - ditambah mutu_formulir.periode_id dan mutu_ci_sessions
  - akun uji: ppm/ppm123, auditor/auditor123, auditee/auditee123, auditor2/auditor123
  - audit 1 DRAFT, 2 SELESAI, 3 PROSES

Cara pakai:
    python3 tests/preview/siapkan.py
    htdocs = /home/user/preview/app
    php -S 0.0.0.0:8090 -t <htdocs> tests/preview/router.php
"""

import hashlib
import io
import os
import re
import shutil
import sqlite3

REPO = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
PREV = os.path.join(os.path.dirname(REPO), 'preview')
APP = os.path.join(PREV, 'app')
DB = os.path.join(PREV, 'mutu.sqlite')
DUMP = os.path.join(REPO, 'database', '05_09_2025.sql')
AWAL = 'harness'


# --------------------------------------------------------------------------
# 1. Salin aplikasi
# --------------------------------------------------------------------------
def salin_app():
    if os.path.isdir(APP):
        shutil.rmtree(APP)
    shutil.copytree(REPO, APP, ignore=shutil.ignore_patterns(
        '.git', 'node_modules', 'preview', '*.pyc', '__pycache__'))


# --------------------------------------------------------------------------
# 2. Parser dump MySQL -> SQLite
# --------------------------------------------------------------------------
def buang_komentar(teks):
    teks = re.sub(r'/\*!\d{5}.*?\*/;', '', teks, flags=re.S)
    return re.sub(r'^--.*$', '', teks, flags=re.M)


def potong_pernyataan(teks):
    hasil, pola, i, n = [], re.compile(
        r'\b(CREATE TABLE IF NOT EXISTS|INSERT INTO)\s+`([A-Za-z_0-9]+)`', re.I), 0, len(teks)
    while True:
        m = pola.search(teks, i)
        if not m:
            break
        j, k, dalam = m.start(), m.start(), False
        while k < n:
            c = teks[k]
            if dalam:
                if c == '\\':
                    k += 2
                    continue
                if c == "'":
                    dalam = False
            elif c == "'":
                dalam = True
            elif c == ';':
                break
            k += 1
        hasil.append((m.group(2), teks[j:k + 1]))
        i = k + 1
    return hasil


def tipe_sqlite(tipe):
    t = tipe.lower()
    if 'int' in t:
        return 'INTEGER'
    if any(x in t for x in ('decimal', 'double', 'float', 'numeric')):
        return 'REAL'
    if 'blob' in t or 'binary' in t:
        return 'BLOB'
    return 'TEXT'


def buat_tabel(nama, pernyataan):
    badan = re.search(r'\((.*)\n\)\s*ENGINE', pernyataan, re.S)
    if not badan:
        return None
    kolom, pk = [], []
    for baris in badan.group(1).split('\n'):
        b = baris.strip().rstrip(',')
        if not b:
            continue
        if re.match(r'PRIMARY KEY', b, re.I):
            pk += re.findall(r'`(\w+)`', b)
            continue
        if re.match(r'(UNIQUE KEY|KEY|CONSTRAINT|FULLTEXT|INDEX)', b, re.I):
            continue
        m = re.match(r'`(\w+)`\s+([A-Za-z]+(?:\([^)]*\))?)', b)
        if m:
            kolom.append((m.group(1), m.group(2), 'auto_increment' in b.lower(),
                          'not null' in b.lower()))
    if not kolom:
        return None
    auto = len(pk) == 1 and any(k[0] == pk[0] and k[2] for k in kolom)
    isi = []
    for nama_k, tipe, _a, wajib in kolom:
        if auto and nama_k == pk[0]:
            isi.append('`%s` INTEGER PRIMARY KEY AUTOINCREMENT' % nama_k)
            continue
        isi.append('`%s` %s%s' % (nama_k, tipe_sqlite(tipe), ' NOT NULL' if wajib else ''))
    if pk and not auto:
        isi.append('PRIMARY KEY (%s)' % ', '.join('`%s`' % p for p in pk))
    return 'CREATE TABLE IF NOT EXISTS `%s` (\n  %s\n)' % (nama, ',\n  '.join(isi))


PETA_ESCAPE = {'0': '\0', 'n': '\n', 'r': '\r', 't': '\t', 'Z': '\x1a',
               'b': '\b', '\\': '\\', "'": "'", '"': '"'}


def buka_string(isi):
    keluar, i = [], 0
    while i < len(isi):
        c = isi[i]
        if c == '\\' and i + 1 < len(isi):
            keluar.append(PETA_ESCAPE.get(isi[i + 1], isi[i + 1]))
            i += 2
            continue
        if c == "'" and i + 1 < len(isi) and isi[i + 1] == "'":
            keluar.append("'")
            i += 2
            continue
        keluar.append(c)
        i += 1
    return ''.join(keluar)


def nilai_mysql(v):
    v = v.strip()
    if v.upper() == 'NULL':
        return None
    if v[:1] == "'":
        return buka_string(v[1:-1])
    if re.fullmatch(r'-?\d+', v):
        return int(v)
    if re.fullmatch(r'-?\d*\.\d+(?:[eE][-+]?\d+)?', v):
        return float(v)
    return v


def pecah_nilai(teks):
    nilai, buf, dalam, i = [], [], False, 0
    while i < len(teks):
        c = teks[i]
        if dalam:
            if c == '\\':
                buf.append(teks[i:i + 2])
                i += 2
                continue
            if c == "'":
                dalam = False
            buf.append(c)
        elif c == "'":
            dalam = True
            buf.append(c)
        elif c == ',':
            nilai.append(nilai_mysql(''.join(buf)))
            buf = []
        else:
            buf.append(c)
        i += 1
    if ''.join(buf).strip() or nilai:
        nilai.append(nilai_mysql(''.join(buf)))
    return nilai


def isi_tuple(badan):
    hasil, i, n = [], 0, len(badan)
    while i < n:
        while i < n and badan[i] in ' \t\r\n,':
            i += 1
        if i >= n or badan[i] != '(':
            break
        i += 1
        buf, dalam = [], False
        while i < n:
            c = badan[i]
            if dalam:
                if c == '\\':
                    buf.append(badan[i:i + 2])
                    i += 2
                    continue
                if c == "'":
                    dalam = False
                buf.append(c)
            elif c == "'":
                dalam = True
                buf.append(c)
            elif c == ')':
                break
            else:
                buf.append(c)
            i += 1
        hasil.append(pecah_nilai(''.join(buf)))
        i += 1
    return hasil


def muat_dump():
    if os.path.exists(DB):
        os.remove(DB)
    con = sqlite3.connect(DB)
    cur = con.cursor()
    gagal = 0
    for nama, pernyataan in potong_pernyataan(buang_komentar(
            io.open(DUMP, encoding='utf-8', errors='replace').read())):
        if pernyataan.upper().startswith('CREATE TABLE'):
            sql = buat_tabel(nama, pernyataan)
            if not sql:
                gagal += 1
                continue
            try:
                cur.execute(sql)
            except sqlite3.Error as e:
                gagal += 1
                print('  [dump] gagal buat %s: %s' % (nama, e))
            continue
        m = re.match(r'INSERT INTO `\w+`\s*\(([^)]*)\)\s*VALUES', pernyataan, re.S)
        if not m:
            gagal += 1
            continue
        kolom = [k.strip().strip('`') for k in m.group(1).split(',')]
        baris = [b for b in isi_tuple(pernyataan[m.end():].rstrip(';').strip())
                 if len(b) == len(kolom)]
        if not baris:
            continue
        try:
            cur.executemany('INSERT INTO `%s` (%s) VALUES (%s)' % (
                nama, ', '.join('`%s`' % k for k in kolom), ', '.join('?' * len(kolom))), baris)
        except sqlite3.Error as e:
            gagal += 1
            print('  [dump] gagal isi %s: %s' % (nama, e))
    con.commit()
    con.close()
    return gagal


# --------------------------------------------------------------------------
# 3. Struktur mirip database nyata + tabel bantu
# --------------------------------------------------------------------------
def siapkan_struktur():
    con = sqlite3.connect(DB)
    cur = con.cursor()
    cur.execute('DROP TABLE IF EXISTS mutu_lingkup')  # versi lama: tanpa lingkup
    cur.execute("""
        CREATE TABLE IF NOT EXISTS mutu_lampiran (
          lampiran_id     INTEGER PRIMARY KEY AUTOINCREMENT,
          audit_id        INTEGER NOT NULL,
          lingkup_id      INTEGER,
          users_id        INTEGER,
          lampiran_nama   TEXT NOT NULL,
          lampiran_asli   TEXT NOT NULL,
          lampiran_tipe   TEXT,
          lampiran_ukuran INTEGER DEFAULT 0,
          lampiran_create TEXT
        )
    """)
    cur.execute("""
        CREATE TABLE IF NOT EXISTS mutu_ci_sessions (
          id         TEXT NOT NULL PRIMARY KEY,
          ip_address TEXT NOT NULL,
          timestamp  INTEGER NOT NULL DEFAULT 0,
          data       BLOB NOT NULL
        )
    """)
    if 'periode_id' not in [r[1] for r in cur.execute('PRAGMA table_info(mutu_formulir)')]:
        cur.execute('ALTER TABLE mutu_formulir ADD COLUMN periode_id INTEGER')
    con.commit()
    con.close()


# --------------------------------------------------------------------------
# 4. Data uji
# --------------------------------------------------------------------------
def seed():
    con = sqlite3.connect(DB)
    cur = con.cursor()

    for users_id, nama, unit, username, sandi, role in (
            (9001, 'PPM Uji', 'PPM', 'ppm', 'ppm123', 'PPM'),
            (9002, 'Auditor Uji', 'Auditor', 'auditor', 'auditor123', 'AUDITOR'),
            (9003, 'Auditee Uji', 'Program Studi Uji', 'auditee', 'auditee123', 'AUDITEE'),
            (9004, 'Auditor Dua', 'Auditor', 'auditor2', 'auditor123', 'AUDITOR')):
        cur.execute('DELETE FROM mutu_users WHERE users_id = ?', (users_id,))
        cur.execute('INSERT INTO mutu_users (users_id, nama, unitkerja, username, password, role,'
                    ' valid) VALUES (?,?,?,?,?,?,1)',
                    (users_id, nama, unit, username,
                     hashlib.md5(sandi.encode()).hexdigest(), role))

    cur.execute('DELETE FROM mutu_periode WHERE periode_id IN (1,2)')
    for pid, tahun, mulai, selesai, aktif in (
            (1, '2025', '2025-11-01', '2025-12-30', 1),
            (2, '2026', '2026-01-01', '2026-12-31', 0)):
        cur.execute('INSERT INTO mutu_periode (periode_id, periode_tahun, periode_mulai,'
                    ' periode_selesai, periode_aktif, periode_create) VALUES (?,?,?,?,?,?)',
                    (pid, tahun, mulai, selesai, aktif, '2026-10-01 21:15:31'))
    cur.execute('UPDATE mutu_formulir SET periode_id = 1')

    for audit_id, status in ((1, 'DRAFT'), (2, 'SELESAI'), (3, 'PROSES')):
        cur.execute('UPDATE mutu_audit SET audit_status = ?, auditee_id = 9003, auditor_id = 9002,'
                    ' auditee = ?, auditor = ?, periode_id = 1 WHERE audit_id = ?',
                    (status, 'Auditee Uji', 'Auditor Uji', audit_id))

    cur.execute('UPDATE mutu_auditjawab SET jwb_jawaban = NULL WHERE audit_id = 1')
    cur.execute("UPDATE mutu_auditjawab SET jwb_jawaban = '<p>Jawaban auditee untuk pertanyaan '"
                " || dtform_id || '.</p>' WHERE audit_id = 2 AND (jwb_jawaban IS NULL OR"
                " trim(jwb_jawaban) = '')")

    for audit_id in (1, 3):  # satu butir per audit belum dinilai auditor
        baris = cur.execute('SELECT d.dtjwb_id FROM mutu_auditjawabdetail d JOIN mutu_auditjawab j'
                            ' ON j.jwb_id = d.jwb_id WHERE j.audit_id = ? ORDER BY d.dtjwb_id',
                            (audit_id,)).fetchall()
        if baris:
            cur.execute('UPDATE mutu_auditjawabdetail SET dtjwb_hasil = NULL, dtjwb_temuan = NULL,'
                        ' dtjwb_catatan = NULL WHERE dtjwb_id = ?', (baris[-1][0],))
    con.commit()

    ringkas = {}
    for audit_id in (1, 2, 3):
        ringkas[audit_id] = (
            cur.execute('SELECT COUNT(*) FROM mutu_auditjawab WHERE audit_id = ?',
                        (audit_id,)).fetchone()[0],
            cur.execute("SELECT COUNT(*) FROM mutu_auditjawab WHERE audit_id = ? AND"
                        " (jwb_jawaban IS NULL OR trim(jwb_jawaban) = '')",
                        (audit_id,)).fetchone()[0],
            cur.execute('SELECT COUNT(*) FROM mutu_auditjawabdetail d JOIN mutu_auditjawab j'
                        ' ON j.jwb_id = d.jwb_id WHERE j.audit_id = ?', (audit_id,)).fetchone()[0])
    info = (ringkas,
            [r[1] for r in cur.execute('PRAGMA table_info(mutu_auditjawab)')],
            [r[1] for r in cur.execute('PRAGMA table_info(mutu_auditjawabdetail)')],
            [r[1] for r in cur.execute('PRAGMA table_info(mutu_lampiran)')])
    con.close()
    return info


# --------------------------------------------------------------------------
# 5. Tambalan konfigurasi
# --------------------------------------------------------------------------
def tambal_config():
    p = os.path.join(APP, 'index.php')
    s = io.open(p, encoding='utf-8').read()
    lama = "\tcase 'development':\n\t\t error_reporting(-1);"
    assert lama in s, 'blok error_reporting tidak ditemukan'
    io.open(p, 'w', encoding='utf-8').write(s.replace(
        lama, "\tcase 'development':\n"
              "\t\t error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);"))

    p = os.path.join(APP, 'application/config/config.php')
    s = io.open(p, encoding='utf-8').read()
    lama = "if (ENVIRONMENT === 'development') {\n    $config['base_url'] = 'http://localhost:8080/mutu/';\n}"
    assert lama in s, 'base_url development tidak ditemukan'
    io.open(p, 'w', encoding='utf-8').write(s.replace(lama, """if (ENVIRONMENT === 'development') {
    /* Lingkungan uji: ikuti host permintaan. */
    $skema = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1:8090';
    $script = isset($_SERVER['SCRIPT_NAME']) ? str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) : '/';
    $config['base_url'] = $skema . $host . rtrim($script, '/') . '/';
}"""))

    p = os.path.join(APP, 'application/config/database.php')
    s = io.open(p, encoding='utf-8').read()
    awal = s.find("if (ENVIRONMENT === 'development') {")
    m = re.search(r"\$db\['default'\] = array\(.*?\n    \);", s[awal:], re.S)
    assert awal >= 0 and m, 'blok database development tidak ditemukan'
    blok = """$db['default'] = array(
        'dsn'       => '',
        'hostname'  => '',
        'username'  => '',
        'password'  => '',
        'database'  => '%s',
        'dbdriver'  => 'sqlite3',
        'dbprefix'  => 'mutu_',
        'pconnect'  => FALSE,
        'db_debug'  => TRUE,
        'cache_on'  => FALSE,
        'cachedir'  => '',
        'char_set'  => 'utf8',
        'dbcollat'  => 'utf8_general_ci',
        'swap_pre'  => '',
        'encrypt'   => FALSE,
        'compress'  => FALSE,
        'stricton'  => FALSE,
        'failover'  => array(),
        'save_queries' => TRUE
    );""" % DB
    io.open(p, 'w', encoding='utf-8').write(s[:awal + m.start()] + blok + s[awal + m.end():])


def main():
    print('[siapkan] salin aplikasi -> %s' % APP)
    salin_app()
    print('[siapkan] muat dump -> %s' % DB)
    print('[siapkan] dump selesai (gagal=%d)' % muat_dump())
    siapkan_struktur()
    ringkas, kolom_jawab, kolom_detail, kolom_lampiran = seed()
    tambal_config()
    print('[siapkan] kolom auditjawab: %s' % ', '.join(kolom_jawab))
    print('[siapkan] kolom auditjawabdetail: %s' % ', '.join(kolom_detail))
    print('[siapkan] kolom lampiran: %s' % ', '.join(kolom_lampiran))
    for audit_id, (tanya, belum, butir) in ringkas.items():
        print('[siapkan] audit %d: %d pertanyaan (%d belum dijawab), %d butir tilik'
              % (audit_id, tanya, belum, butir))
    print('[siapkan] akun: ppm/ppm123, auditor/auditor123, auditee/auditee123, auditor2/auditor123')
    print('[siapkan] selesai')


if __name__ == '__main__':
    main()
