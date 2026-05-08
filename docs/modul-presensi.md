# 🛠️ Daftar Lengkap Perintah Artisan — UKM MCI Presensi

### Laravel 13 + Filament v5 + Sanctum

> Jalankan semua perintah dari **root folder proyek Laravel** Anda.
> Urutan langkah harus diikuti dari atas ke bawah.

---

## 📋 DAFTAR ISI

1. [Install Dependensi](#1-install-dependensi)
2. [Publish & Install Vendor](#2-publish--install-vendor)
3. [Generate Migration](#3-generate-migration)
4. [Jalankan Migration](#4-jalankan-migration)
5. [Generate Models](#5-generate-models)
6. [Generate Filament Resources](#6-generate-filament-resources)
7. [Generate API Controllers](#7-generate-api-controllers)
8. [Generate Form Requests (Opsional)](#8-generate-form-requests-opsional)
9. [Generate Seeder & Factory](#9-generate-seeder--factory)
10. [Buat Admin User](#10-buat-admin-user)
11. [Cache & Optimasi](#11-cache--optimasi)
12. [Development Server](#12-development-server)
13. [Perintah Tambahan Berguna](#13-perintah-tambahan-berguna)

---

## 1. Install Dependensi

```bash
# Install Laravel Sanctum (API Authentication)
composer require laravel/sanctum

# Install Filament v3 (Admin Panel)
composer require filament/filament:"^3.2"

# Install Simple QR Code (untuk generate QR di Filament)
composer require simplesoftwareio/simple-qrcode

# [Opsional] Install dependensi pengembangan
composer require --dev barryvdh/laravel-debugbar
composer require --dev laravel/telescope
```

---

## 2. Publish & Install Vendor

```bash
# Publish migration & config Sanctum
php artisan vendor:publish \
    --provider="Laravel\Sanctum\SanctumServiceProvider" \
    --tag="sanctum-migrations"

php artisan vendor:publish \
    --provider="Laravel\Sanctum\SanctumServiceProvider" \
    --tag="sanctum-config"

# Install & setup Filament Panel (interaktif, akan membuat AdminPanelProvider)
php artisan filament:install --panels

# [Opsional] Publish config Simple QR Code
php artisan vendor:publish \
    --provider="SimpleSoftwareIO\QrCode\QrCodeServiceProvider"
```

---

## 3. Generate Migration

```bash
# Migration tabel agendas
php artisan make:migration create_agendas_table

# Migration tabel presensis
php artisan make:migration create_presensis_table

# [Jika perlu menambah kolom ke tabel yang sudah ada]
php artisan make:migration add_qr_code_token_to_agendas_table --table=agendas
php artisan make:migration add_role_to_users_table --table=users
```

> ⚠️ Setelah generate, **ganti isi file** migration dengan kode yang sudah disediakan.

---

## 4. Jalankan Migration

```bash
# Jalankan semua migration
php artisan migrate

# Jalankan migration + seed data awal
php artisan migrate --seed

# [Reset & ulangi dari awal — HATI-HATI: hapus semua data!]
php artisan migrate:fresh

# [Reset + seed ulang]
php artisan migrate:fresh --seed

# [Lihat status migration]
php artisan migrate:status

# [Rollback migration terakhir]
php artisan migrate:rollback

# [Rollback N step]
php artisan migrate:rollback --step=2
```

---

## 5. Generate Models

```bash
# Model Agenda (dengan migration, factory, seeder, resource)
php artisan make:model Agenda -mfsr

# Model Presensi (dengan migration & factory)
php artisan make:model Presensi -mf

# Flags tersedia:
# -m  = buat migration sekaligus
# -f  = buat factory sekaligus
# -s  = buat seeder sekaligus
# -r  = buat resource controller sekaligus
# -c  = buat controller sekaligus
# --all = buat semua (migration, factory, seeder, policy, controller, resource)
```

> ⚠️ Ganti isi `app/Models/Agenda.php`, `Presensi.php`, dan `User.php`
> dengan kode yang sudah disediakan.

---

## 6. Generate Filament Resources

```bash
# AgendaResource — dengan halaman View (untuk tampil QR Code)
php artisan make:filament-resource Agenda --generate --view

# PresensiResource — simple (hanya List page, sesuai kebutuhan read-only)
php artisan make:filament-resource Presensi --simple

# [Jika ingin generate semua halaman secara manual]
php artisan make:filament-page ListAgendas --resource=AgendaResource
php artisan make:filament-page CreateAgenda --resource=AgendaResource
php artisan make:filament-page ViewAgenda --resource=AgendaResource
php artisan make:filament-page EditAgenda --resource=AgendaResource

# [Membuat Filament Widget untuk dashboard statistik presensi]
php artisan make:filament-widget PresensiStatsWidget --stats-overview

# [Membuat Filament Widget tabel terbaru]
php artisan make:filament-widget RecentPresensiWidget --table

# [Membuat Relation Manager (jika ingin tampilkan presensi di dalam halaman Agenda)]
php artisan make:filament-relation-manager AgendaResource presensis user.name

# [Membuat Custom Filament Page]
php artisan make:filament-page RekapPresensi
```

> ⚠️ Ganti isi semua file Resource & Pages dengan kode yang sudah disediakan.

---

## 7. Generate API Controllers

```bash
# Controller untuk Autentikasi API
php artisan make:controller Api/AuthController

# Controller untuk Presensi API
php artisan make:controller Api/PresensiController

# [Opsional] Controller dengan resource methods (index, store, show, dll)
php artisan make:controller Api/AgendaController --api

# [Generate API Resource untuk transformasi JSON]
php artisan make:resource PresensiResource
php artisan make:resource AgendaResource
php artisan make:resource UserResource

# [Generate API Resource Collection]
php artisan make:resource PresensiCollection --collection
```

> ⚠️ Ganti isi controller dengan kode yang disediakan.
> Salin juga isi `routes/api.php` dari kode yang disediakan.

---

## 8. Generate Form Requests (Opsional)

> Berguna untuk memisahkan logika validasi dari Controller.

```bash
# Form Request untuk validasi presensi
php artisan make:request StorePresensiRequest

# Form Request untuk validasi agenda
php artisan make:request StoreAgendaRequest
php artisan make:request UpdateAgendaRequest

# Form Request untuk autentikasi
php artisan make:request LoginRequest
php artisan make:request RegisterRequest
```

---

## 9. Generate Seeder & Factory

```bash
# Seeder untuk data awal
php artisan make:seeder UserSeeder
php artisan make:seeder AgendaSeeder
php artisan make:seeder PresensiSeeder

# Factory untuk testing
php artisan make:factory AgendaFactory --model=Agenda
php artisan make:factory PresensiFactory --model=Presensi

# Jalankan seeder tertentu
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=AgendaSeeder

# Jalankan semua seeder
php artisan db:seed
```

### Contoh isi `database/seeders/AgendaSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        Agenda::create([
            'nama_agenda'  => 'Rapat Pleno UKM MCI',
            'deskripsi'    => 'Rapat pleno perdana semester genap.',
            'waktu_mulai'  => now()->addDay(),
            'waktu_selesai' => now()->addDay()->addHours(3),
            'lokasi'       => 'Aula Kampus Lt. 3',
            'is_active'    => true,
            // qr_code_token akan di-generate otomatis
        ]);
    }
}
```

---

## 10. Buat Admin User

```bash
# Buat user admin Filament secara interaktif
php artisan make:filament-user

# [Atau buat via Tinker]
php artisan tinker
>>> \App\Models\User::create([
...     'name'     => 'Admin MCI',
...     'email'    => 'admin@ukm-mci.ac.id',
...     'password' => bcrypt('password123'),
... ]);
>>> exit
```

---

## 11. Cache & Optimasi

```bash
# Clear semua cache (jalankan setelah ubah kode)
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# [Shortcut: clear semua sekaligus]
php artisan optimize:clear

# Cache untuk production (jalankan saat deploy)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

# [Lihat semua route yang terdaftar]
php artisan route:list

# [Lihat route API saja]
php artisan route:list --path=api
```

---

## 12. Development Server

```bash
# Jalankan server development (default: localhost:8000)
php artisan serve

# Jalankan di port tertentu
php artisan serve --port=8080

# Jalankan di host tertentu (untuk akses dari perangkat lain di jaringan)
php artisan serve --host=0.0.0.0 --port=8000

# Jalankan Queue Worker (jika menggunakan job/notification)
php artisan queue:work

# Jalankan Queue Worker dengan restart otomatis
php artisan queue:work --tries=3

# Jalankan Scheduler (untuk cron job)
php artisan schedule:work
```

---

## 13. Perintah Tambahan Berguna

```bash
# Lihat semua perintah artisan yang tersedia
php artisan list

# Lihat detail perintah tertentu
php artisan help make:model

# Generate APP_KEY (jika belum ada di .env)
php artisan key:generate

# Buat Symbolic Link storage (untuk file upload)
php artisan storage:link

# Masuk ke Tinker (REPL interaktif)
php artisan tinker

# Contoh Tinker — Cek token agenda
php artisan tinker
>>> \App\Models\Agenda::first()->qr_code_token
>>> \App\Models\Presensi::with('user','agenda')->latest()->first()
>>> exit

# Generate Sanctum Token untuk testing API di Tinker
php artisan tinker
>>> $user = \App\Models\User::first();
>>> $token = $user->createToken('test-token')->plainTextToken;
>>> echo $token;
>>> exit

# Lihat semua tabel & kolom (via Tinker)
php artisan tinker
>>> Schema::getColumnListing('agendas');
>>> Schema::getColumnListing('presensis');
>>> exit

# [Policy — jika ingin tambahkan otorisasi per role]
php artisan make:policy AgendaPolicy --model=Agenda
php artisan make:policy PresensiPolicy --model=Presensi

# [Observer — alternatif dari booted() method]
php artisan make:observer AgendaObserver --model=Agenda

# [Event & Listener — jika ingin kirim notifikasi saat presensi]
php artisan make:event PresensiDicatat
php artisan make:listener KirimNotifikasiPresensi --event=PresensiDicatat

# [Mail — jika ingin kirim email konfirmasi presensi]
php artisan make:mail KonfirmasiPresensiMail

# [Notification — untuk push notification]
php artisan make:notification PresensiTercatatNotification
```

---

## 🚀 Urutan Setup Cepat (Copy-Paste)

```bash
# ── SATU BLOK — jalankan semua sekaligus ──────────────────────

composer require laravel/sanctum filament/filament:"^3.2" simplesoftwareio/simple-qrcode && \
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-migrations" && \
php artisan filament:install --panels && \
php artisan make:migration create_agendas_table && \
php artisan make:migration create_presensis_table && \
php artisan make:model Agenda && \
php artisan make:model Presensi && \
php artisan make:filament-resource Agenda --generate --view && \
php artisan make:filament-resource Presensi --simple && \
php artisan make:controller Api/AuthController && \
php artisan make:controller Api/PresensiController && \
echo "✅ Semua file berhasil di-generate! Sekarang salin kode yang disediakan."
```

> Setelah perintah di atas selesai, **salin kode** dari file-file yang sudah disediakan
> ke masing-masing file yang baru ter-generate, lalu jalankan:

```bash
php artisan migrate && \
php artisan make:filament-user && \
php artisan optimize:clear && \
php artisan serve
```

---

## 🌐 Akses Aplikasi

| Panel          | URL                                              |
| -------------- | ------------------------------------------------ |
| Admin Filament | `http://localhost:8000/admin`                    |
| API Login      | `POST http://localhost:8000/api/auth/login`      |
| API Presensi   | `POST http://localhost:8000/api/presensi`        |
| API Riwayat    | `GET http://localhost:8000/api/presensi/riwayat` |
