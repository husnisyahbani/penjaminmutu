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
- Daftar tilik pada halaman audit (auditor/admin) terisi otomatis dari butir
  lingkup, jadi tidak ada lagi tombol **Tambah Tilik**.
