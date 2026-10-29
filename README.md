# JurnalGuru — Sistem Catatan Mengajar Guru

Aplikasi web **JurnalGuru** (EduJurnal) untuk SMK Muhammadiyah 04 Boyolali: jadwal mengajar, jurnal harian, absensi, nilai harian, pencarian, rekap semester (PDF), data master, backup & restore.

## Persyaratan

- PHP 8.2+ (proyek ini diuji dengan PHP 8.5)
- Composer
- MySQL 8+ (produksi) atau SQLite (pengembangan lokal)
- Ekstensi PHP: pdo, mbstring, openssl, tokenizer, xml, ctype, json, fileinfo

Panduan lengkap untuk laptop baru (Windows, belum ada PHP): **[PANDUAN-INSTALASI.md](PANDUAN-INSTALASI.md)**.

## Instalasi

```bash
cd D:\JurnalGuru
composer install
cp .env.example .env   # Windows: copy .env.example .env
php artisan key:generate
```

### Database MySQL (produksi)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jurnalguru
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `jurnalguru` lewat phpMyAdmin atau klien MySQL.

### Pengembangan lokal (SQLite)

Default `.env` menggunakan SQLite (`database/database.sqlite`).

### Migrasi & data demo

```env
SEED_DEMO_PASSWORD=password_demo_anda_min_8_karakter
```

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Buka http://127.0.0.1:8000/login

Akun demo (hanya jika seeder dijalankan di `local`/`testing` dan `SEED_DEMO_PASSWORD` diisi):

| Username   | Peran              |
|-----------|---------------------|
| admin     | Pengelola Sistem    |
| kepsek    | Kepala Sekolah      |
| tu        | Staf Tata Usaha     |
| guru.sri  | Guru                |

**Produksi:** jangan set password demo di `.env` publik; buat akun via admin setelah deploy.

## Pengujian

```bash
php artisan test
```

## Backup otomatis harian

Perintah: `php artisan jurnalguru:backup` (dijadwalkan 02:00).

Aktifkan scheduler di server:

- **Linux cron:** `* * * * * cd /path/to/JurnalGuru && php artisan schedule:run >> /dev/null 2>&1`
- **Windows Task Scheduler:** jalankan `php artisan schedule:run` setiap menit.

Backup disimpan di `storage/app/backups` (bukan folder publik).

## Restore

Menu **Admin → Backup**: pilih file backup terdaftar, centang konfirmasi, submit. Hanya admin.

## Peran pengguna

- **Guru:** dashboard, isi/ubah/hapus jurnal sendiri, cari & rekap milik sendiri.
- **Kepala Sekolah / TU:** cari catatan & rekap (baca saja, tanpa ubah akademik).
- **Admin:** CRUD master data, jadwal, backup/restore (tanpa mengisi jurnal).

## Desain UI

Antarmuka mengikuti **Genesis** (`genesis-DESIGN.md`): tipografi General Sans + DM Sans, warna indigo `#6366F1`, kartu flat dengan border, navigasi atas sticky + drawer mobile. Stylesheet: `public/css/genesis.css`.

## Fitur selesai

- [x] Autentikasi username + middleware peran
- [x] Migrations & relasi sesuai ERD final binder
- [x] Dashboard guru (jadwal hari ini, pengingat ≤7 hari)
- [x] Jurnal + absensi + nilai (transaksi DB, unique schedule+date)
- [x] Pencarian & rekap + PDF DomPDF
- [x] CRUD master (admin)
- [x] Backup manual, log, restore terbatas, scheduler
- [x] Tes PHPUnit inti

## Catatan teknis

- Zona waktu: set `APP_TIMEZONE=Asia/Jakarta` di `.env` untuk produksi.
- `APP_DEBUG=false` di produksi; gunakan HTTPS.
- Asumsi: admin tidak otomatis melihat semua data akademik (sesuai binder final).

## Lisensi

Proyek pendidikan / skripsi — SMK Muhammadiyah 04 Boyolali.
