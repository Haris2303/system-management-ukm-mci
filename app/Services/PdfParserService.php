<?php
// ── app/Services/PdfParserService.php ────────────────────────
// Parsing PDF menjadi chunks teks siap di-embed.
// Menggunakan library: smalot/pdfparser (composer require smalot/pdfparser)

namespace App\Services;

use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class PdfParserService
{
    /**
     * Ukuran chunk dalam karakter.
     * ~500 karakter ≈ ~120 token — ideal untuk context window.
     */
    private int $chunkSize    = 500;
    private int $chunkOverlap = 70;   // Overlap antar chunk untuk konteks

    /**
     * Parse PDF dari path file, return array of chunk strings.
     *
     * @param  string  $filePath  Absolute path ke file PDF
     * @return string[]
     */
    public function parse(string $filePath): array
    {
        $parser   = new Parser();
        $pdf      = $parser->parseFile($filePath);
        $fullText = $pdf->getText();

        // Bersihkan teks
        $fullText = $this->cleanText($fullText);

        if (empty(trim($fullText))) {
            return [];
        }

        return $this->splitIntoChunks($fullText);
    }

    /**
     * Bersihkan teks dari karakter tidak perlu.
     */
    private function cleanText(string $text): string
    {
        // Hapus karakter control (kecuali newline)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        // Normalkan spasi
        $text = preg_replace('/[ \t]+/', ' ', $text);

        // Normalkan newline berlebihan (maks 2 baris kosong)
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * Pecah teks menjadi chunks dengan overlap.
     * Mencoba memecah di batas kalimat/paragraf supaya konteks lebih alami.
     *
     * @return string[]
     */
    private function splitIntoChunks(string $text): array
    {
        $chunks = [];

        // Pisahkan per paragraf dulu
        $paragraphs = preg_split('/\n{2,}/', $text);
        $current    = '';

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (empty($para)) continue;

            // Jika menambahkan paragraf ini masih dalam batas chunk size
            if (mb_strlen($current) + mb_strlen($para) + 2 <= $this->chunkSize) {
                $current .= ($current ? "\n\n" : '') . $para;
            } else {
                // Simpan chunk sekarang jika tidak kosong
                if (!empty($current)) {
                    $chunks[] = $current;
                }

                // Jika paragraf sendiri sudah melebihi chunk size, pecah per kalimat
                if (mb_strlen($para) > $this->chunkSize) {
                    $sentenceChunks = $this->splitBySentence($para);
                    // Simpan semua kecuali terakhir — yang terakhir jadi current
                    $lastIdx = count($sentenceChunks) - 1;
                    foreach ($sentenceChunks as $idx => $sc) {
                        if ($idx < $lastIdx) {
                            $chunks[] = $sc;
                        } else {
                            $current = $sc;
                        }
                    }
                } else {
                    // Overlap: ambil akhir chunk sebelumnya sebagai awalan
                    $overlap = '';
                    if (!empty($chunks)) {
                        $lastChunk = end($chunks);
                        $overlap   = mb_substr($lastChunk, -$this->chunkOverlap);
                    }
                    $current = $overlap ? $overlap . ' ' . $para : $para;
                }
            }
        }

        // Simpan sisa
        if (!empty(trim($current))) {
            $chunks[] = $current;
        }

        // Filter chunks terlalu pendek (< 50 karakter = noise)
        return array_values(array_filter($chunks, fn($c) => mb_strlen(trim($c)) >= 50));
    }

    /**
     * Pecah teks per kalimat (untuk paragraf panjang).
     *
     * @return string[]
     */
    private function splitBySentence(string $text): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        $chunks    = [];
        $current   = '';

        foreach ($sentences as $sentence) {
            if (mb_strlen($current) + mb_strlen($sentence) + 1 <= $this->chunkSize) {
                $current .= ($current ? ' ' : '') . $sentence;
            } else {
                if (!empty($current)) $chunks[] = $current;
                $current = $sentence;
            }
        }
        if (!empty(trim($current))) $chunks[] = $current;

        return $chunks;
    }
}
