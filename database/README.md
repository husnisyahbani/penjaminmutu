# Folder `database`

Berisi skrip SQL yang perlu dijalankan pada database aplikasi (MySQL/MariaDB,
prefix tabel default `mutu_`).

## `pengaturan_home.sql`

Membuat tabel pengaturan tampilan halaman depan beserta konten bawaannya:

| Tabel              | Isi                                                                        |
| ------------------ | -------------------------------------------------------------------------- |
| `mutu_home_setting` | pengaturan nilai tunggal: nama sistem, hero, profil, warna tema, kontak, dll |
| `mutu_home_item`    | konten berulang: kartu akses, galeri profil, misi, tupoksi, sasaran mutu     |

Cara pakai:

1. Buka phpMyAdmin (atau menu SQL) pada database aplikasi.
2. Impor berkas `database/pengaturan_home.sql`.
3. Sesuaikan nama tabel bila prefix pada `application/config/database.php`
   bukan `mutu_`.
4. Setelah diimpor, pengaturan dapat diubah dari aplikasi melalui menu-menu
   pada menu **Tampilan Beranda** (Identitas & Tema, Hero /
   Banner, Kartu Akses, dst.).

> Tabel juga dapat dibuat langsung dari aplikasi: buka salah satu menu
> pengaturan beranda (menu **Tampilan Beranda**), lalu klik tombol
> **Buat Tabel Pengaturan** (muncul otomatis bila tabel belum ada). Selama
> tabel belum dibuat, halaman depan tetap tampil normal menggunakan konten
> bawaan.

## `ci_sessions.sql`

Membuat tabel penyimpanan sesi login. Aplikasi memakai session driver
`database`, jadi **tanpa tabel ini halaman login akan gagal** (*Database Error*).

| Objek              | Isi                                                            |
| ------------------ | -------------------------------------------------------------- |
| `mutu_ci_sessions` | sesi login: `id`, `ip_address`, `timestamp`, `data`             |

Cara pakai:

1. Buka phpMyAdmin (atau menu SQL) pada database aplikasi.
2. Impor berkas `database/ci_sessions.sql`.
3. Sesuaikan nama tabel bila prefix pada `application/config/database.php`
   bukan `mutu_` (contoh tanpa prefix disediakan di bagian bawah berkas).
4. Tidak ada data awal yang perlu diisi: baris sesi dibuat otomatis saat
   pengguna login.

Catatan:

- Konfigurasi memakai `$config['sess_driver'] = 'database'` dan
  `$config['sess_save_path'] = 'ci_sessions'`; prefix `mutu_` ditambahkan
  otomatis oleh CodeIgniter, sehingga nama tabel sesungguhnya
  `mutu_ci_sessions`.
- Sesi kedaluwarsa dibersihkan otomatis (masa berlaku bawaan 7200 detik).
  Untuk memaksa semua pengguna login ulang: `TRUNCATE TABLE mutu_ci_sessions;`.

## `periode_audit.sql`

Membuat tabel periode audit dan menghubungkannya ke daftar audit:

| Objek                          | Isi                                                                 |
| ------------------------------ | ------------------------------------------------------------------- |
| `mutu_periode`                 | daftar periode: `periode_tahun`, `periode_mulai`, `periode_selesai`, penanda `periode_aktif` |
| `mutu_audit`.`periode_id`      | relasi setiap audit ke periodenya                                   |

Cara pakai:

1. Buka phpMyAdmin (atau menu SQL) pada database aplikasi.
2. Impor berkas `database/periode_audit.sql`.
3. Sesuaikan nama tabel bila prefix pada `application/config/database.php`
   bukan `mutu_`.
4. Kelola periode dari aplikasi melalui menu **Audit → Periode**.

Catatan:

- Hanya satu periode yang aktif. Periode aktif menjadi **filter bawaan** pada
  halaman **Audit → Daftar Audit**; bila tidak ada periode aktif, halaman
  tersebut menampilkan seluruh audit.
- Status aktif dapat dibatalkan kembali lewat tombol **Batalkan aktif** pada
  kolom **Aksi** baris periode yang sedang aktif (menu **Audit → Periode**).
- Audit yang baru dibuat (dari admin, auditor, maupun auditee) otomatis
  ditempatkan pada periode yang sedang aktif bila ada.
- Tabel dan kolom di atas juga dapat dibuat langsung dari aplikasi: buka menu
  **Audit → Periode**, lalu klik tombol **Buat Tabel Periode** (muncul
  otomatis bila tabel belum ada). Aman dijalankan berulang kali.

## `formulir_periode.sql`

Menghubungkan formulir audit (**Audit → Formulir Audit**) dengan periode.

| Objek                    | Isi                                                              |
| ------------------------ | ---------------------------------------------------------------- |
| `mutu_formulir`.`periode_id` | relasi formulir ke `mutu_periode`; `NULL` = belum berperiode |

Cara pakai:

1. Pastikan `database/periode_audit.sql` sudah diimpor (tabel `mutu_periode`).
2. Impor berkas `database/formulir_periode.sql` melalui phpMyAdmin.
3. Sesuaikan prefix tabel bila bukan `mutu_`.

Catatan:

- Kolom ini juga dapat dibuat langsung dari aplikasi: menu **Audit → Periode**,
  tombol **Buat Tabel Periode** (muncul otomatis bila belum siap). Aman
  dijalankan berulang kali.
- Halaman **Formulir Audit** dan daftar formulir pada halaman auditor memiliki
  **filter periode** dengan bawaan **periode aktif**; bila tidak ada periode
  aktif, seluruh formulir ditampilkan.
- Formulir yang belum berperiode (`periode_id NULL`) selalu ikut tampil.
- Formulir baru otomatis ditempatkan pada periode yang sedang aktif, dan
  periodenya dapat diubah dari form tambah/edit (termasuk dilepas kembali ke
  **Tanpa Periode**).
- Periode yang masih dipakai formulir tidak dapat dihapus dari halaman
  **Periode**; pindahkan formulirnya lebih dahulu. Pada tingkat database
  relasinya memakai `ON DELETE SET NULL`, jadi formulir tidak pernah ikut
  terhapus.
- Berkas ini tidak idempoten (`ADD COLUMN` gagal bila kolom sudah ada) —
  lewati bila kolom `periode_id` sudah terpasang.

## `urut_pertanyaan.sql`

Menambah kolom urutan pertanyaan pada formulir audit. Halaman **Audit → Formulir Audit**
(lihat detail satu formulir) kini berbentuk kursus: **pertanyaan** dan
**lingkup**, keduanya dapat digeser naik/turun.

| Objek                        | Isi                                                   |
| ---------------------------- | ----------------------------------------------------- |
| `mutu_detailform`.`dtform_urut` | nomor urut pertanyaan di dalam satu formulir (1, 2, 3, …) |

```sql
ALTER TABLE `mutu_detailform`
  ADD COLUMN `dtform_urut` int(11) NOT NULL DEFAULT 0 AFTER `dtform_lingkup`;
```

Catatan:

- Isi awal disusun mengikuti urutan `dtform_id` (lihat berkas SQL), jadi tampilan
  tidak berubah sebelum ada topik yang digeser.
- Urutan **lingkup** memakai kolom `mutu_lingkup`.`lingkup_urut` yang sudah ada
  (dibuat oleh `migrasi_lingkup.sql`), tidak perlu kolom baru.
- Bisa juga dipasang dari aplikasi tanpa impor SQL: buka detail formulir, klik
  tombol **Aktifkan Urutan Pertanyaan** pada pemberitahuan di atas daftar pertanyaan.
- Tidak idempoten (`ADD COLUMN` gagal bila kolom sudah ada) — lewati bila kolom
  `dtform_urut` sudah terpasang.
- Tanpa kolom ini aplikasi tetap berjalan; tombol naik/turun pertanyaan
  dinonaktifkan dan urutan memakai `dtform_id`.

## `migrasi_lingkup.sql`

Memindahkan lingkup pertanyaan dari satu kolom teks menjadi **banyak butir**
pada tabel tersendiri, dan menghubungkan jawaban audit ke butir tersebut:

| Objek                        | Isi                                                                       |
| ---------------------------- | ------------------------------------------------------------------------- |
| `mutu_lingkup`               | butir lingkup: `dtform_id`, `lingkup_urut`, `lingkup_isi`                  |
| `mutu_auditjawab`.`lingkup_id` | relasi jawaban ke butir; `NULL` = jawaban tingkat pertanyaan             |
| `mutu_auditjawabdetail`.`lingkup_id` | penanda baris tilik lama yang sudah dipindahkan ke bentuk baru     |
| `mutu_detailform`.`dtform_lingkup` | kolom lama, boleh kosong, dihapus setelah migrasi                  |

Cara pakai:

1. Impor berkas `database/migrasi_lingkup.sql` melalui phpMyAdmin.
2. Buka menu **Audit → Formulir Audit** (halaman pertanyaan) atau langsung
   `admin/migrasi`, lalu klik **Jalankan Migrasi**. Aplikasi memecah isi kolom
   lama menjadi baris `mutu_lingkup` (satu baris per butir) dan memindahkan
   baris daftar tilik lama ke jawaban per butir bila teksnya cocok. Aman
   dijalankan berulang kali.
3. Periksa hasilnya (jumlah butir, contoh pemecahan) pada halaman yang sama.
4. Bila sudah benar, klik **Hapus Kolom Lama** untuk membuang
   `mutu_detailform.dtform_lingkup`.

Catatan:

- Jawaban yang sudah ada tidak hilang: baris tilik lama yang teks pertanyaannya
  cocok dengan sebuah butir dipindahkan ke jawaban butir tersebut, sedangkan
  yang tidak cocok tetap tersimpan dan tetap ditampilkan (kolom `lingkup_id`
  bernilai `NULL`).
- Butir yang sudah dipakai jawaban audit tidak ikut terhapus saat pertanyaan
  disunting, supaya hasil audit tidak kehilangan relasinya.
- Daftar tilik pada halaman audit (auditor) diisi **auditor**: hasil, temuan,
  dan catatan tiap butir tilik disimpan pada `mutu_auditjawabdetail`
  (`dtjwb_hasil`, `dtjwb_temuan`, `dtjwb_catatan`). Baris butir dapat
  ditambah/disunting lewat halaman **Daftar Tilik** (tombol *Tambah Butir*).
- **Auditee tidak mengisi `mutu_auditjawabdetail`.** Yang dijawab auditee ada
  pada halaman *Auditee → Daftar Audit → Detail*, yaitu **tiap butir lingkup**
  pada tabel `mutu_lingkup`: jawaban tersimpan pada `mutu_auditjawab` dengan
  kolom `lingkup_id` terisi. Pertanyaan yang belum punya butir lingkup tetap
  memakai jawaban tingkat pertanyaan (`lingkup_id` bernilai `NULL`), supaya
  formulir lama tidak terkunci. Lampiran mengikuti butirnya
  (`mutu_lampiran`.`jwb_id` + `lingkup_id`). Rencana koreksi tiap butir
  (`mutu_auditjawabdetail`.`dtjwb_koreksi`) baru diisi auditee setelah audit
  berstatus **SELESAI**.

## `lampiran_lingkup.sql`

Membuat tabel lampiran jawaban audit. Pada halaman
**Auditee → Daftar Audit → (detail)**, daftar pertanyaan yang dijawab berasal
dari `mutu_lingkup` dan **setiap butir lingkup wajib dijawab**; tiap jawaban
boleh dilengkapi **lampiran opsional lebih dari satu berkas**. Butir tilik
(`mutu_auditjawabdetail`) hanya diisi auditor, auditee tidak menjawabnya.

| Objek             | Isi                                                                              |
| ----------------- | -------------------------------------------------------------------------------- |
| `mutu_lampiran`   | berkas lampiran per jawaban: `audit_id`, `jwb_id` (baris `mutu_auditjawab` — baris butir lingkup bila ada), `lingkup_id` (butir lingkup), `dtjwb_id` (baris tilik lama), `users_id`, `lampiran_nama` (nama simpan), `lampiran_asli` (nama asli), `lampiran_tipe`, `lampiran_ukuran`, `lampiran_create` |

Cara pakai:

1. Buka phpMyAdmin (atau menu SQL) pada database aplikasi.
2. Impor berkas `database/lampiran_lingkup.sql`.
3. Sesuaikan nama tabel bila prefix pada `application/config/database.php`
   bukan `mutu_`.
4. Pastikan folder penyimpanan `filedata/lampiran/` dapat ditulis oleh web server
   (dibuat otomatis oleh aplikasi saat unggahan pertama).

Catatan:

- Aman dijalankan berulang kali (`CREATE TABLE IF NOT EXISTS`).
- Tabel lama yang belum punya `jwb_id` dapat disiapkan lewat
  **Admin → Migrasi Lingkup** (tombol *Siapkan Kolom Tilik*). Lampiran lama
  tetap terbaca: bila `jwb_id` kosong, lampiran dipetakan lewat
  `lingkup_id` (= `dtform_id` pertanyaannya).
- Relasi memakai `ON DELETE CASCADE`: menghapus audit ikut membersihkan
  lampirannya, sedangkan jawaban (`mutu_auditjawab`) tidak terpengaruh.
- Tanpa tabel ini aplikasi tetap berjalan: kolom jawaban wajib tetap ada, tetapi
  bagian lampiran menampilkan keterangan bahwa fitur belum disiapkan.
- Lampiran bersifat **opsional**; satu pertanyaan boleh memiliki banyak berkas.
  Jenis berkas: PDF, Office (doc/docx/xls/xlsx/ppt/pptx), gambar
  (jpg/jpeg/png), dan arsip (zip/rar), maksimum 5 MB per berkas.
- Menghapus lampiran dari halaman detail ikut menghapus berkas fisiknya.
- Rollback: `DROP TABLE mutu_lampiran;` (berkas fisik di `filedata/lampiran/`
  dihapus manual bila perlu).

## `hapus_kolom_auditjawab.sql`

Menghapus kolom penilaian lama pada `mutu_auditjawab`:
`jwb_pertanyaan`, `jwb_referensi`, `jwb_temuan`, `jwb_hasil`, `jwb_catatan`,
dan `jwb_koreksi`.

Kolom-kolom itu tidak dipakai lagi. Penilaian auditor (hasil, temuan, catatan)
dan rencana koreksi auditee kini tersimpan **per butir tilik** pada
`mutu_auditjawabdetail` (`dtjwb_hasil`, `dtjwb_temuan`, `dtjwb_catatan`,
`dtjwb_koreksi`), sedangkan `mutu_auditjawab` hanya menyimpan **jawaban
auditee** (`jwb_jawaban`) dan **tujuan pertanyaan** (`jwb_tujuan`).

| Data                     | Tempat baru                                                          |
| ------------------------ | -------------------------------------------------------------------- |
| Jawaban auditee          | `mutu_auditjawab`.`jwb_jawaban` (per butir lingkup: `lingkup_id` terisi) |
| Tujuan pertanyaan        | `mutu_auditjawab`.`jwb_tujuan`                                        |
| Hasil / temuan / catatan | `mutu_auditjawabdetail`.`dtjwb_hasil` / `dtjwb_temuan` / `dtjwb_catatan` |
| Referensi butir          | `mutu_auditjawabdetail`.`dtjwb_referensi`                             |
| Rencana koreksi          | `mutu_auditjawabdetail`.`dtjwb_koreksi`                               |

Cara pakai:

1. **Perbarui kode aplikasi lebih dahulu**, pastikan kolom-kolom di atas sudah
   tidak dibaca/ditulis lagi.
2. Cadangkan basis data.
3. Impor berkas `database/hapus_kolom_auditjawab.sql` lewat phpMyAdmin (tab SQL).
4. Sesuaikan prefix `mutu_` bila berbeda.

Catatan:

- Tidak aman dijalankan berulang kali: `DROP COLUMN` akan gagal bila kolomnya
  sudah terlanjur dihapus. Pesan galat itu bisa diabaikan.
- Rencana koreksi **wajib** memakai `mutu_auditjawabdetail`.`dtjwb_koreksi`.
  Bila kolom itu belum ada, impor `database/koreksi_butir.sql` atau jalankan
  **Admin → Migrasi Lingkup → Siapkan Kolom Koreksi Butir**. Tanpa kolom itu
  rencana koreksi tidak dapat disimpan.
- Migrasi jawaban lama (`Admin → Migrasi Lingkup`) kini hanya memindahkan
  **penanda** `lingkup_id` pada baris tilik, bukan nilai penilaian - nilai
  penilaian tetap dibaca langsung dari `mutu_auditjawabdetail`.
- Rollback: buat kembali kolom-kolomnya bila benar-benar diperlukan
  (`ALTER TABLE mutu_auditjawab ADD COLUMN jwb_hasil text ...`), tetapi data
  yang sudah dihapus tidak dapat dikembalikan tanpa cadangan.
