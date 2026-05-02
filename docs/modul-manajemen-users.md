# 👤 User Management & Role-Based Access Control

## 📦 Step 1: Install Spatie Laravel Permission

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Ini akan menambahkan migrations untuk tabel: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`.

## 🗄️ Step 2: Jalankan Migration

```bash
php artisan migrate
```

Lalu tambahkan migration baru saya untuk kolom profile di users:

```bash
# File: database/migrations/2024_01_11_add_profile_to_users_table.php
php artisan migrate
```

## 🌱 Step 3: Jalankan Seeder

```bash
# 1. Seed roles & permissions
php artisan db:seed --class=RolePermissionSeeder

# 2. Buat super admin awal
php artisan db:seed --class=SuperAdminSeeder
```

Setelah seeder, Anda akan dapat:

- 6 role (super_admin, ketua_ukm, sekretaris, bendahara, ketua_divisi, anggota)
- 1 akun super admin awal:
    - Email: `superadmin@mci.ac.id`
    - Password: `superadmin123`

## 🔧 Step 4: Update Model User

Timpa file `app/Models/User.php` dengan versi baru saya yang sudah include:

- `HasRoles` trait dari Spatie
- Implementasi `FilamentUser` interface
- Method `canAccessPanel()` untuk gate panel admin
- Helper methods (`isSuperAdmin()`, `isKetuaDivisi()`, `getRoleLabelAttribute()`)
- Tambahkan `divisi_id`, `no_hp`, `avatar` ke `$fillable`

## 🔐 Step 5: Apply Access Control ke Resources

Buka file `PATCH_ALL_RESOURCES.php` — di dalamnya ada method `canViewAny()` (dan beberapa `getEloquentQuery()`) yang perlu Anda salin ke masing-masing Resource yang sudah ada.

Contoh untuk `PostResource.php`:

```php
class PostResource extends Resource
{
    // ... kode yang sudah ada ...

    // ⭐ Tambahkan ini:
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('kelola_berita') ?? false;
    }
}
```

## 🎯 Step 6: Update Action "Luluskan"

Buka file `PendaftarResource_LULUSKAN_PATCH.php` dan timpa action `luluskan` lama dengan versi baru yang:

- Menyimpan `divisi_id` ke user
- Auto-assign role `anggota` agar user bisa akses mobile API

## 📊 Matriks Role & Akses

| Resource                    | super_admin | ketua_ukm | sekretaris | bendahara | ketua_divisi | anggota |
| --------------------------- | :---------: | :-------: | :--------: | :-------: | :----------: | :-----: |
| **Pengguna (User)**         |     ✅      |    ❌     |     ❌     |    ❌     |      ❌      |   ❌    |
| **Berita**                  |     ✅      |    ✅     |     ✅     |    ❌     |      ❌      |   ❌    |
| **Galeri/Program/Pengurus** |     ✅      |    ✅     |     ✅     |    ❌     |      ❌      |   ❌    |
| **Materi (semua)**          |     ✅      |    ✅     |     ✅     |    ❌     |     🟡\*     |   ❌    |
| **Agenda & Presensi**       |     ✅      |    ✅     |     ✅     |    ❌     |      ❌      |   ❌    |
| **E-Voting**                |     ✅      |    ✅     |     ❌     |    ❌     |      ❌      |   ❌    |
| **Tagihan & Transaksi Kas** |     ✅      |    ✅     |     ❌     |    ✅     |      ❌      |   ❌    |
| **Divisi**                  |     ✅      |    ✅     |     ❌     |    ❌     |      ❌      |   ❌    |
| **Pertanyaan Seleksi**      |     ✅      |    ✅     |     ❌     |    ❌     |     🟡\*     |   ❌    |
| **Pendaftar**               |     ✅      |    ✅     |     ❌     |    ❌     |     🟡\*     |   ❌    |
| **Akses Panel Admin**       |     ✅      |    ✅     |     ✅     |    ✅     |      ✅      |   ❌    |

🟡\* = Hanya yang terkait divisinya sendiri

## 🔍 Cara Kerja Scoping (Ketua Divisi)

```php
// PendaftarResource → getEloquentQuery()
if ($user->can('kelola_pendaftar_divisi') && $user->divisi_id) {
    return $query->where('divisi_id', $user->divisi_id);
}
```

Ketua Divisi yang login dengan `divisi_id = 3` akan **otomatis** hanya melihat:

- Pendaftar di divisi 3
- Pertanyaan seleksi divisi 3
- Materi divisi 3 + materi umum (`divisi_id = NULL`)

Ini berlaku di **semua Resource yang ada `getEloquentQuery()`-nya** — admin tidak perlu tambahkan filter manual.

## 🚪 Akses Panel Admin

Method `canAccessPanel()` di `User` model mengecek permission `akses_panel_admin`. Hanya 5 role yang punya permission ini:

- ✅ super_admin
- ✅ ketua_ukm
- ✅ sekretaris
- ✅ bendahara
- ✅ ketua_divisi
- ❌ anggota (tidak bisa login ke `/admin`, hanya bisa via mobile API)

Saat anggota mencoba akses URL `/admin`, mereka langsung redirect ke login — bahkan setelah login, mereka tidak bisa masuk panel.

## 📁 Struktur File yang Dibuat

```
app/Models/User.php                                       ← TIMPA
app/Filament/Resources/UserResource.php                   ← BARU
app/Filament/Resources/UserResource/Pages/                ← BARU (3 files)
database/migrations/2024_01_11_add_profile_to_users_table.php  ← BARU
database/seeders/RolePermissionSeeder.php                 ← BARU
database/seeders/SuperAdminSeeder.php                     ← BARU
```

## 🔐 Keamanan

1. **Super Admin tidak bisa hapus dirinya sendiri** — `canDelete()` cek `auth()->id() !== $record->id`
2. **Password di-hash otomatis** — `dehydrateStateUsing(fn ($state) => Hash::make($state))`
3. **Password tidak ter-overwrite saat edit jika kosong** — `dehydrated(fn ($state) => filled($state))`
4. **Role tersimpan via Spatie relationship** — atomic, tidak ada race condition

## ⚡ Action Tambahan di UserResource

| Tombol                | Fungsi                                   |
| --------------------- | ---------------------------------------- |
| **🔑 Reset Password** | Reset ke `password123` dengan konfirmasi |
| **✏️ Edit**           | Edit data user + roles                   |
| **🗑️ Delete**         | Hapus user (kecuali diri sendiri)        |
