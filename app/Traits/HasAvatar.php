<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasAvatar
{
    public static function resolveAvatarUrl(?string $avatar): ?string
    {
        if (! $avatar) return null;

        if (str_starts_with($avatar, 'emoji:')) {
            [, $emoji, $color] = explode(':', $avatar, 3);
            return static::buildEmojiSvgDataUri($emoji, $color);
        }

        if (str_starts_with($avatar, 'http')) return $avatar;

        return Storage::disk('public')->url($avatar);
    }

    public static function buildEmojiSvgDataUri(string $emoji, string $bgColor): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">'
            . '<circle cx="64" cy="64" r="64" fill="#' . htmlspecialchars($bgColor) . '"/>'
            . '<text x="64" y="68" font-size="72" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($emoji) . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return static::resolveAvatarUrl($this->avatar);
    }
}
