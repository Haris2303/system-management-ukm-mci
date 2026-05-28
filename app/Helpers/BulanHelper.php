<?php

namespace App\Helpers;

class BulanHelper
{
    public const NAMA_BULAN = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    /**
     * Ubah format YYYY-MM menjadi "Nama Bulan YYYY".
     * Contoh: "2025-01" → "Januari 2025"
     */
    public static function format(string $bulanTagihan): string
    {
        if (! preg_match('/^(\d{4})-(\d{2})$/', $bulanTagihan, $m)) {
            return $bulanTagihan;
        }

        return (self::NAMA_BULAN[$m[2]] ?? $m[2]) . ' ' . $m[1];
    }

    /**
     * Hasilkan array opsi ['YYYY-MM' => 'Nama Bulan YYYY']
     * dari N bulan ke belakang hingga N bulan ke depan.
     */
    public static function options(int $sebelum = 6, int $sesudah = 6): array
    {
        $options = [];
        $now     = now('Asia/Jayapura');

        for ($i = -$sebelum; $i <= $sesudah; $i++) {
            $date          = $now->copy()->addMonths($i)->startOfMonth();
            $key           = $date->format('Y-m');
            $options[$key] = self::NAMA_BULAN[$date->format('m')] . ' ' . $date->format('Y');
        }

        return $options;
    }
}
