# 🤖 Panduan Setup RAG Chatbot — UKM MCI

### Claude API + PDF Knowledge Base + Bubble Chat

---

## ⚡ Perintah Artisan & Composer

```bash
# 1. Install library PDF parser
composer require smalot/pdfparser

# 2. Generate migration
php artisan make:migration create_rag_tables

# 3. Generate models
php artisan make:model RagDocument
php artisan make:model RagChunk
php artisan make:model ChatHistory

# 4. Generate Job (background processing)
php artisan make:job ProcessPdfJob

# 5. Generate Service classes
# (buat manual di app/Services/)

# 6. Generate Controller
php artisan make:controller ChatbotController

# 7. Generate Filament Resource
php artisan make:filament-resource RagDocument --generate

# 8. Jalankan migration
php artisan migrate

# 9. Setup queue worker (untuk background PDF processing)
# Opsi A — Database queue (paling mudah, tanpa Redis)
php artisan queue:table
php artisan migrate

# Ubah QUEUE_CONNECTION di .env:
# QUEUE_CONNECTION=database

# Jalankan worker:
php artisan queue:work --queue=default

# Opsi B — Sync (tanpa background, langsung proses saat upload)
# QUEUE_CONNECTION=sync
```

---

## 🔑 Konfigurasi .env

Tambahkan ke file `.env` Anda:

```env
# ── Claude API (WAJIB) ──────────────────────────────────────────
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# ── Embedding Provider ──────────────────────────────────────────
# Untuk development (gratis, tanpa API):
EMBEDDING_PROVIDER=tfidf

# Untuk production (akurasi lebih baik):
# Daftar gratis di https://www.voyageai.com
EMBEDDING_PROVIDER=voyage
EMBEDDING_API_KEY=pa-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
EMBEDDING_MODEL=voyage-3-lite

# ── Queue ───────────────────────────────────────────────────────
QUEUE_CONNECTION=database
```

---

## 📁 Daftar File — Salin ke Proyek

| File                      | Lokasi                                              |
| ------------------------- | --------------------------------------------------- |
| `create_rag_tables.php`   | `database/migrations/`                              |
| `RagDocument.php`         | `app/Models/`                                       |
| `RagChunk.php`            | `app/Models/`                                       |
| `ChatHistory.php`         | `app/Models/`                                       |
| `EmbeddingService.php`    | `app/Services/` (buat folder baru)                  |
| `PdfParserService.php`    | `app/Services/`                                     |
| `RagService.php`          | `app/Services/`                                     |
| `ProcessPdfJob.php`       | `app/Jobs/`                                         |
| `ChatbotController.php`   | `app/Http/Controllers/`                             |
| `RagDocumentResource.php` | `app/Filament/Resources/`                           |
| `Pages/` (2 files)        | `app/Filament/Resources/RagDocumentResource/Pages/` |
| `rag.php`                 | `config/`                                           |
| `chatbot.blade.php`       | `resources/views/components/`                       |
| `web.php`                 | `routes/` (timpa yang lama)                         |

---

## 🔗 Integrasi ke Layout

Buka `resources/views/layouts/app.blade.php` dan tambahkan:

### Di dalam `<head>`:

```html
<meta name="csrf-token" content="{{ csrf_token() }}" />
```

### Sebelum `</body>`:

```html
@include('components.chatbot')
@stack('scripts')
</body>
```

---

## 📋 Alur Kerja RAG Lengkap

```
Admin upload PDF
    └── Filament: /admin/rag-documents/create
        └── ProcessPdfJob (background)
            ├── PdfParserService → parse & chunk teks
            └── EmbeddingService → generate vector per chunk
                └── Simpan ke tabel rag_chunks ✅

User ketik pertanyaan di bubble chat
    └── POST /chatbot/chat
        └── ChatbotController::chat()
            ├── RagService::retrieve()
            │   ├── Embed query → vector
            │   └── Cosine similarity → top 4 chunks
            └── RagService::generate()
                ├── Bangun system prompt + konteks chunks
                ├── Kirim ke Claude API (streaming)
                └── SSE stream → bubble chat real-time ✅
```

---

## 🎯 Register Service di AppServiceProvider

Tambahkan di `app/Providers/AppServiceProvider.php`:

```php
use App\Services\EmbeddingService;
use App\Services\RagService;

public function register(): void
{
    $this->app->singleton(EmbeddingService::class);
    $this->app->singleton(RagService::class);
}
```

---

## 🌐 Endpoint API

| Method | URL                    | Keterangan                          |
| ------ | ---------------------- | ----------------------------------- |
| POST   | `/chatbot/chat`        | Kirim pesan, response SSE streaming |
| GET    | `/chatbot/suggested`   | Pertanyaan saran                    |
| GET    | `/chatbot/status/{id}` | Status processing PDF               |
| POST   | `/chatbot/upload`      | Upload PDF (admin, auth required)   |

---

## 💡 Tips Penggunaan

**Dokumen PDF yang baik untuk RAG:**

- Profil & sejarah UKM MCI
- Panduan pendaftaran anggota
- Daftar program dan divisi
- FAQ yang sering ditanya mahasiswa
- Jadwal kegiatan dan syarat bergabung

**Ukuran optimal:** PDF 5–50 halaman, teks bisa di-select (bukan scan foto)

---

## 🔒 Keamanan

- Route `/chatbot/upload` sudah dilindungi middleware `auth`
- Session chat menggunakan UUID unik per browser
- Riwayat chat disimpan di database (bukan di client)
- Tidak ada data sensitif yang dikembalikan ke client
