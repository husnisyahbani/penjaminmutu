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
