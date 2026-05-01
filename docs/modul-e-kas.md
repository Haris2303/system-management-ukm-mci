# 💰 Modul E-Kas (Keuangan) — UKM MCI

## ⚡ Perintah Setup

```bash
# 1. Generate file scaffolding
php artisan make:migration create_ekas_tables
php artisan make:model TagihanKas
php artisan make:model TransaksiKas

php artisan make:filament-resource TagihanKas --generate
php artisan make:filament-resource TransaksiKas --generate

php artisan make:filament-widget KasWidget --stats-overview
php artisan make:controller Api/KasController

# 2. Set timezone aplikasi (PENTING!)
# Edit config/app.php:
#   'timezone' => 'Asia/Jayapura',
# (atau biarkan UTC dan pakai timezone() per query — sudah dilakukan di kode ini)

# 3. Jalankan migration
php artisan migrate

# 4. Clear cache
php artisan optimize:clear
```

## 📁 Struktur File

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── TagihanKasResource.php          ← CRUD + Action "Tandai Lunas"
│   │   ├── TagihanKasResource/Pages/       (3 files)
│   │   ├── TransaksiKasResource.php        ← CRUD kas masuk/keluar
│   │   └── TransaksiKasResource/Pages/     (3 files)
│   └── Widgets/
│       └── KasWidget.php                   ← Stats di dashboard
├── Http/Controllers/Api/
│   └── KasController.php                   ← 3 endpoint mobile
├── Models/
│   ├── TagihanKas.php
│   └── TransaksiKas.php
├── Services/
│   └── KasService.php                      ← ⭐ Logic perhitungan saldo (shared)
database/migrations/
└── ..._create_ekas_tables.php
routes/
└── api.php
```

## 🎯 Filament v5 — Catatan Penting

Kode ini sudah disesuaikan dengan **Filament v5** (yang Anda gunakan):

| Hal                   | Filament v5 (kode ini)                                | Filament v3 (lama)     |
| --------------------- | ----------------------------------------------------- | ---------------------- |
| Form definition       | `public static function form(Schema $schema): Schema` | `Form $form`           |
| Method                | `$schema->components([...])`                          | `$form->schema([...])` |
| Import                | `Filament\Schemas\Schema`                             | `Filament\Forms\Form`  |
| Navigation icon type  | `\BackedEnum\|string\|null`                           | `?string`              |
| Navigation group type | `\UnitEnum\|string\|null`                             | `?string`              |

## 🌏 Timezone Asia/Jayapura

Semua operasi waktu menggunakan timezone Sintang (WIT, UTC+9):

- ✅ `now('Asia/Jayapura')` di action "Tandai Lunas"
- ✅ `->timezone('Asia/Jayapura')` di DateTimePicker & TextColumn
- ✅ `->setTimezone('Asia/Jayapura')` di response API
- ✅ Format tanggal Indonesia: `translatedFormat('d F Y, H:i')` + suffix "WIT"

## 🔧 Register Widget di Dashboard

Edit `app/Providers/Filament/AdminPanelProvider.php` (atau panel provider Anda):

```php
use App\Filament\Widgets\KasWidget;

->widgets([
    Widgets\AccountWidget::class,
    KasWidget::class,                        // ← Tambahkan
    Widgets\FilamentInfoWidget::class,
])
```

Atau jika ingin tampil di SEMUA halaman (bukan hanya dashboard), register sebagai widget global di panel provider.

## 📡 API Endpoint Reference

Semua endpoint di-protect oleh `auth:sanctum` middleware.

### `GET /api/kas/tunggakan`

Response:

```json
{
    "pesan": "Anda memiliki 2 tagihan yang belum dibayar.",
    "data": {
        "jumlah_tunggakan": 2,
        "total_nominal": 100000,
        "total_format": "Rp 100.000",
        "tagihan": [
            {
                "id": 5,
                "bulan_tagihan": "2025-04",
                "bulan_tagihan_format": "April 2025",
                "nominal": 50000,
                "nominal_format": "Rp 50.000",
                "status": "belum_dibayar",
                "catatan": null,
                "dibuat_pada": "01 Apr 2025"
            }
        ]
    }
}
```

### `GET /api/kas/riwayat`

Response:

```json
{
    "pesan": "Riwayat pembayaran berhasil dimuat.",
    "data": {
        "jumlah_pembayaran": 5,
        "total_dibayar": 250000,
        "total_dibayar_format": "Rp 250.000",
        "riwayat": [
            {
                "id": 12,
                "bulan_tagihan_format": "Maret 2025",
                "nominal_format": "Rp 50.000",
                "tanggal_bayar_format": "15 Maret 2025, 14:30",
                "catatan": null
            }
        ]
    }
}
```

### `GET /api/kas/saldo-transparansi`

Response:

```json
{
    "pesan": "Saldo kas organisasi berhasil dimuat.",
    "data": {
        "total_saldo": 2750000,
        "total_saldo_format": "Rp 2.750.000",
        "rincian": {
            "iuran_lunas": { "nominal": 1500000, "format": "Rp 1.500.000" },
            "kas_masuk": { "nominal": 2000000, "format": "Rp 2.000.000" },
            "kas_keluar": { "nominal": 750000, "format": "Rp 750.000" }
        },
        "diperbarui_pada": "01 Mei 2025, 14:35 WIT"
    }
}
```

## 🎨 Fitur Bonus yang Saya Tambahkan

Selain spesifikasi minimum, ada beberapa fitur tambahan:

1. **Bulk Action "Tandai Lunas Massal"** — Bendahara bisa centang banyak tagihan sekaligus
2. **Action "Batalkan Lunas"** — Untuk koreksi jika ada salah input
3. **Kolom `catatan`** di tagihan — Catatan opsional dari Bendahara
4. **Foto bukti** di transaksi kas — Upload kuitansi
5. **Filter range tanggal** di tabel transaksi
6. **Auto-fill `dicatat_oleh`** dengan ID bendahara yang login
7. **Navigation badge** — Tampil jumlah tagihan belum dibayar di sidebar
8. **Polling 30 detik** di widget — Saldo auto-refresh
9. **Mini chart** di widget saldo — Visual trend
10. **Helper formats** — `bulan_tagihan_format` ("April 2025"), `nominal_format` ("Rp 50.000")
11. **Unique constraint** `[user_id, bulan_tagihan]` — Cegah duplikasi tagihan
12. **Service Layer (`KasService`)** — Logic perhitungan saldo terpusat, dipakai widget DAN API (DRY)
