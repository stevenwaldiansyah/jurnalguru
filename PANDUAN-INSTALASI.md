# Panduan Instalasi JurnalGuru

Panduan ini untuk menjalankan **JurnalGuru — Sistem Catatan Mengajar Guru** (SMK Muhammadiyah 04 Boyolali) di laptop/PC **Windows** yang belum terpasang PHP, Composer, atau database.

---

## A. Isi paket yang dikirim

Pastikan folder proyek berisi kode Laravel (`app`, `routes`, `database`, `resources`, `public`, `composer.json`, `.env.example`, dll.).

**Tidak perlu dikirim** (akan dibuat ulang di laptop baru):

- Folder `vendor/` → diisi dengan `composer install`
- File `.env` → dibuat dari `.env.example` (jangan kirim `.env` lama; berisi kunci rahasia)

**Opsional:**

- `database/database.sqlite` — hanya jika ingin membawa data; untuk instalasi bersih, biarkan kosong dan jalankan migrasi + seeder.

---

## B. Software yang harus diinstall

### 1. Laragon (disarankan) atau XAMPP

| Opsi | Unduh | Kegunaan |
|------|--------|----------|
| **Laragon** | https://laragon.org/ | PHP, MySQL, Apache sekaligus |
| **XAMPP** | https://www.apachefriends.org/ | PHP, MySQL, Apache sekaligus |

Setelah install, **nyalakan MySQL** (Laragon: Start All / XAMPP: MySQL Start).

### 2. Composer

- Unduh: https://getcomposer.org/download/
- Pilih **Composer-Setup.exe** untuk Windows.
- Saat diminta lokasi PHP, arahkan ke:
  - Laragon: `C:\laragon\bin\php\php-8.x\php.exe`
  - XAMPP: `C:\xampp\php\php.exe`

### 3. Cek instalasi

Buka **PowerShell** atau **CMD**:

```powershell
php -v
composer -V
```

Harus menampilkan versi PHP (minimal 8.2) dan Composer tanpa error.

---

## C. Memasang proyek

### 1. Extract folder proyek

Extract zip ke folder kerja, contoh:

- Laragon: `C:\laragon\www\JurnalGuru`
- XAMPP: `C:\xampp\htdocs\JurnalGuru`

### 2. Buka terminal di folder proyek

```powershell
cd C:\laragon\www\JurnalGuru
```

*(sesuaikan path dengan lokasi extract)*

### 3. Dependensi PHP

```powershell
composer install
```

Tunggu sampai selesai (butuh koneksi internet).

### 4. File environment

```powershell
copy .env.example .env
php artisan key:generate
```

### 5. Pengaturan `.env` penting

Buka file `.env` dengan Notepad atau editor teks.

**Nama aplikasi & bahasa:**

```env
APP_NAME=JurnalGuru
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_TIMEZONE=Asia/Jakarta
```

**Password untuk akun demo (hanya pengembangan / presentasi):**

```env
SEED_DEMO_PASSWORD=PasswordDemoAndaMin8Karakter
```

Ganti `PasswordDemoAndaMin8Karakter` dengan sandi yang Anda tentukan (minimal 8 karakter). **Jangan pakai sandi ini di server produksi nyata.**

---

## D. Database

Pilih **satu** cara di bawah.

### Opsi 1 — SQLite (paling mudah, tanpa MySQL)

Di `.env`:

```env
DB_CONNECTION=sqlite
```

Buat file database kosong:

```powershell
New-Item -ItemType File -Path database\database.sqlite -Force
```

Migrasi + data demo:

```powershell
php artisan migrate:fresh --seed
```

### Opsi 2 — MySQL (disarankan untuk demo sekolah)

1. Buka **phpMyAdmin** (Laragon/XAMPP) atau HeidiSQL.
2. Buat database baru: `jurnalguru` (collation: `utf8mb4_unicode_ci`).

Di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jurnalguru
DB_USERNAME=root
DB_PASSWORD=
```

*(Isi `DB_PASSWORD` jika MySQL memakai sandi.)*

```powershell
php artisan migrate:fresh --seed
```

---

## E. Menjalankan aplikasi

Di folder proyek:

```powershell
php artisan serve
```

Buka browser:

**http://127.0.0.1:8000/login**

Untuk menghentikan server: tekan `Ctrl+C` di terminal.

---

## F. Akun demo (setelah seeder berhasil)

Seeder hanya membuat akun demo jika `SEED_DEMO_PASSWORD` di `.env` **sudah diisi** dan environment `local` (default saat development).

| Username | Peran |
|----------|--------|
| `admin` | Pengelola Sistem |
| `kepsek` | Kepala Sekolah |
| `tu` | Staf Tata Usaha |
| `guru.sri` | Guru |

**Password:** sama dengan nilai `SEED_DEMO_PASSWORD` di `.env`.

---

## G. Pengujian (opsional)

```powershell
php artisan test
```

Semua tes seharusnya lulus jika instalasi benar.

---

## H. Backup database

- **Manual:** login sebagai `admin` → menu **Backup** → **Jalankan Backup Manual**.
- File backup tersimpan di `storage/app/backups` (bukan folder publik).
- Teks **“Backup otomatis harian pukul 02:00”** = jadwal di kode; agar benar-benar jalan otomatis di Windows, perlu **Task Scheduler** yang setiap menit menjalankan `php artisan schedule:run` di folder proyek. Untuk tugas sekolah, backup manual biasanya sudah cukup.

---

## I. Masalah umum

| Gejala | Solusi |
|--------|--------|
| `'php' is not recognized` | PHP belum di PATH; gunakan terminal dari Laragon (**Terminal** menu) atau tambahkan folder PHP ke PATH Windows. |
| `'composer' is not recognized` | Install ulang Composer dan pilih path `php.exe` yang benar. |
| Error koneksi MySQL | Pastikan MySQL jalan; cek `DB_DATABASE`, username, password di `.env`. |
| Seeder tidak membuat user | Pastikan `SEED_DEMO_PASSWORD` di `.env` terisi, lalu `php artisan db:seed --class=DemoDataSeeder`. |
| Halaman kosong / error 500 | Set `APP_DEBUG=true` sementara di `.env`, lihat pesan error; jalankan `php artisan config:clear`. |
| Unduh PDF rekap gagal | Pastikan ekstensi PHP `gd` atau `dom` aktif (biasanya sudah di XAMPP/Laragon). |

---

## J. Produksi (jika nanti dipasang di server sekolah)

- Set `APP_ENV=production` dan `APP_DEBUG=false`.
- Jangan gunakan `SEED_DEMO_PASSWORD` atau akun demo di produksi.
- Gunakan HTTPS.
- Buat akun admin baru dengan sandi kuat lewat menu admin setelah deploy.

---

## K. Ringkasan perintah (copy-paste)

```powershell
cd C:\laragon\www\JurnalGuru
composer install
copy .env.example .env
php artisan key:generate
# edit .env: SEED_DEMO_PASSWORD, DB_*, APP_TIMEZONE=Asia/Jakarta
New-Item -ItemType File -Path database\database.sqlite -Force
php artisan migrate:fresh --seed
php artisan serve
```

---

**Instansi:** SMK Muhammadiyah 04 Boyolali  
**Aplikasi:** JurnalGuru — Sistem Catatan Mengajar Guru  

Jika ada langkah yang gagal, catat pesan error lengkap dari terminal atau layar browser untuk dibantu perbaiki.
