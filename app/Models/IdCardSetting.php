<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdCardSetting extends Model
{
    protected $fillable = ['border_template', 'background_image', 'updated_by'];

    // ── Single-row helpers ────────────────────────────────────────

    public static function current(): static
    {
        return static::firstOrCreate([], ['border_template' => 'biru-klasik']);
    }

    public static function activeTemplate(): string
    {
        return static::current()->border_template;
    }

    public static function setTemplate(string $slug): void
    {
        static::current()->update([
            'border_template' => $slug,
            'updated_by'      => auth()->user()?->getKey(),
        ]);
    }

    public static function setBackgroundImage(?string $path): void
    {
        static::current()->update([
            'background_image' => $path,
            'updated_by'       => auth()->user()?->getKey(),
        ]);
    }

    public static function backgroundImageUrl(): ?string
    {
        $path = static::current()->background_image;
        if (!$path) return null;
        return request()->getSchemeAndHttpHost() . '/storage/' . $path;
    }

    // ── Relationship ──────────────────────────────────────────────

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
