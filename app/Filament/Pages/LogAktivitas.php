<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\File;
use UnitEnum;

/**
 * Viewer log aktivitas (storage/logs/activity-*.log). Hanya untuk super_admin.
 */
class LogAktivitas extends Page
{
    /** Batas byte yang dibaca dari akhir file agar halaman tetap ringan. */
    private const MAX_READ_BYTES = 2_000_000;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $navigationLabel = 'Log Aktivitas';

    protected static ?string $title = 'Log Aktivitas';

    protected static ?string $slug = 'log-aktivitas';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.log-aktivitas';

    public ?string $tanggal = null;

    public string $level = '';

    public string $search = '';

    public int $limit = 100;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->tanggal = array_key_first($this->getTanggalOptions());
    }

    /** @return array<string, string> tanggal (Y-m-d) => path file, terbaru dulu */
    public function getTanggalOptions(): array
    {
        $options = [];

        foreach (File::glob(storage_path('logs/activity-*.log')) ?: [] as $path) {
            if (preg_match('/activity-(\d{4}-\d{2}-\d{2})\.log$/', $path, $m)) {
                $options[$m[1]] = $path;
            }
        }

        krsort($options);

        return $options;
    }

    /** @return array<int, array<string, mixed>> */
    public function getEntries(): array
    {
        $path = $this->getTanggalOptions()[$this->tanggal] ?? null;

        if (! $path || ! is_file($path)) {
            return [];
        }

        $size   = filesize($path);
        $handle = fopen($path, 'r');
        fseek($handle, max(0, $size - self::MAX_READ_BYTES));
        $raw = stream_get_contents($handle);
        fclose($handle);

        $entries = [];

        foreach (array_reverse(explode("\n", $raw)) as $line) {
            if (! preg_match('/^\[([\d\-: ]+)\] \w+\.(\w+): (.*)$/', $line, $m)) {
                continue;
            }

            $level = strtolower($m[2]);

            if ($this->level !== '' && $level !== $this->level) {
                continue;
            }

            if ($this->search !== '' && stripos($line, $this->search) === false) {
                continue;
            }

            // "pesan {json konteks} []" -> pisahkan pesan dan konteks
            $message = $m[3];
            $context = '';

            if (($pos = strpos($message, ' {')) !== false) {
                $context = rtrim(substr($message, $pos + 1));
                $context = preg_replace('/ \[\]$/', '', $context);
                $message = substr($message, 0, $pos);
            }

            $decoded = json_decode($context, true);

            $entries[] = [
                'time'    => $m[1],
                'level'   => $level,
                'message' => $message,
                'context' => is_array($decoded)
                    ? json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : $context,
            ];

            if (count($entries) >= $this->limit) {
                break;
            }
        }

        return $entries;
    }
}
