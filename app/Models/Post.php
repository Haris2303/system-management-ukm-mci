<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'thumbnail',
        'ringkasan',
        'konten',
        'kategori',
        'tag',
        'author_id',
        'status',
        'published_at',
        'views',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured'  => 'boolean',
    ];

    // ── Auto-generate slug dari judul ─────────────────────────
    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug($post->judul);
            }
            if ($post->status === 'published' && empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::updating(function (Post $post): void {
            if ($post->isDirty('status') && $post->status === 'published' && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }

    private static function uniqueSlug(string $judul): string
    {
        $base = Str::slug($judul);
        $slug = $base;
        $i    = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    // ── Relationships ─────────────────────────────────────────
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopePublished($q)
    {
        return $q->where('status', 'published')->whereNotNull('published_at');
    }

    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }

    public function scopeByKategori($q, string $kategori)
    {
        return $q->where('kategori', $kategori);
    }

    // ── Helpers ───────────────────────────────────────────────
    public function getTags(): array
    {
        return $this->tag
            ? array_map('trim', explode(',', $this->tag))
            : [];
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /** Warna badge per kategori */
    public function getKategoriColor(): string
    {
        return match ($this->kategori) {
            'Prestasi'    => 'amber',
            'Kegiatan'    => 'emerald',
            'Pengumuman'  => 'rose',
            default       => 'brand',          // Berita
        };
    }

    /** Icon FA per kategori (returns HTML <i> tag) */
    public function getKategoriEmoji(): string
    {
        return match ($this->kategori) {
            'Prestasi'   => '<i class="fa-solid fa-trophy"></i>',
            'Kegiatan'   => '<i class="fa-regular fa-calendar"></i>',
            'Pengumuman' => '<i class="fa-solid fa-bullhorn"></i>',
            default      => '<i class="fa-regular fa-newspaper"></i>',
        };
    }

    /** Estimasi waktu baca */
    public function readTime(): string
    {
        $words = str_word_count(strip_tags($this->konten));
        $mins  = max(1, (int) round($words / 200));
        return "{$mins} menit baca";
    }
}
