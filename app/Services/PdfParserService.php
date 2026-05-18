<?php

namespace App\Services;

use RuntimeException;
use Smalot\PdfParser\Parser;

/**
 * PdfParserService — wrapper untuk smalot/pdfparser
 *
 * Install dependency (jika belum):
 *   composer require smalot/pdfparser
 *
 * Kenapa smalot/pdfparser?
 *   - Pure PHP, tidak butuh binary tambahan
 *   - Bisa extract text dari PDF tanpa perlu pdftotext
 *   - Support PDF terenkripsi & multi-page
 */
class PdfParserService
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Extract text dari file PDF.
     *
     * @param string $filePath Path absolut ke file PDF
     * @return string Teks hasil extraction (bisa multi-line)
     * @throws RuntimeException Kalau file tidak ada atau gagal di-parse
     */
    public function extract(string $filePath): string
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("File PDF tidak ditemukan: {$filePath}");
        }

        if (! is_readable($filePath)) {
            throw new RuntimeException("File PDF tidak bisa dibaca: {$filePath}");
        }

        try {
            $pdf  = $this->parser->parseFile($filePath);
            $text = $pdf->getText();

            // Normalisasi whitespace dan karakter aneh
            $text = $this->cleanText($text);

            if (empty(trim($text))) {
                throw new RuntimeException(
                    'PDF tidak mengandung teks yang bisa diextract. '
                        . 'Mungkin PDF berupa scan/image — perlu OCR untuk diproses.'
                );
            }

            return $text;
        } catch (\Throwable $e) {
            throw new RuntimeException(
                "Gagal parse PDF '{$filePath}': " . $e->getMessage()
            );
        }
    }

    /**
     * Extract text per halaman (kalau butuh metadata page number).
     *
     * @return array<int, string> Array indexed dari halaman 1
     */
    public function extractByPage(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("File PDF tidak ditemukan: {$filePath}");
        }

        $pdf   = $this->parser->parseFile($filePath);
        $pages = $pdf->getPages();
        $result = [];

        foreach ($pages as $i => $page) {
            $result[$i + 1] = $this->cleanText($page->getText());
        }

        return $result;
    }

    /**
     * Bersihkan teks dari karakter aneh & normalisasi whitespace.
     */
    private function cleanText(string $text): string
    {
        // Replace multiple whitespace dengan single space
        $text = preg_replace('/\s+/', ' ', $text);

        // Hapus karakter control non-printable (kecuali newline, tab)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        // Normalisasi line breaks (kalau extractByPage tidak dipakai)
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        return trim($text);
    }
}
