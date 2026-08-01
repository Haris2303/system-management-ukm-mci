<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Catatan: kolom `foto` diisi nama file placeholder (belum ada file fisiknya
     * di storage). Untuk presentasi, unggah ulang file asli lewat panel admin
     * atau salin gambar contoh ke storage/app/public/gallery/.
     */
    public function run(): void
    {
        $galleries = [
            ['judul' => 'Workshop Laravel untuk Anggota Baru', 'kategori' => 'Workshop', 'is_featured' => true],
            ['judul' => 'Pengembangan Sistem Absensi QR Code', 'kategori' => 'Kegiatan', 'is_featured' => false],
            ['judul' => 'Peluncuran Website Resmi UKM MCI', 'kategori' => 'Prestasi', 'is_featured' => true],
            ['judul' => 'Rapat Koordinasi Program Kerja', 'kategori' => 'Rapat', 'is_featured' => false],
            ['judul' => 'Dokumentasi Workshop Figma', 'kategori' => 'Workshop', 'is_featured' => false],
            ['judul' => 'Syuting Video Profil UKM MCI', 'kategori' => 'Kegiatan', 'is_featured' => false],
            ['judul' => 'Game Jam Internal UKM MCI 2025', 'kategori' => 'Prestasi', 'is_featured' => true],
            ['judul' => 'Open Recruitment Anggota Baru', 'kategori' => 'Kegiatan', 'is_featured' => false],
            ['judul' => 'Workshop Unity Dasar', 'kategori' => 'Workshop', 'is_featured' => false],
            ['judul' => 'Kunjungan Studi Banding ke UNS', 'kategori' => 'Kegiatan', 'is_featured' => false],
        ];

        foreach ($galleries as $i => $data) {
            Gallery::firstOrCreate(
                ['judul' => $data['judul']],
                [
                    'foto'        => 'gallery/demo-' . ($i + 1) . '.jpg',
                    'kategori'    => $data['kategori'],
                    'deskripsi'   => 'Dokumentasi kegiatan ' . $data['judul'] . ' UKM MCI.',
                    'is_featured' => $data['is_featured'],
                    'urut'        => $i + 1,
                ]
            );
        }

        $this->command->info('✅ ' . count($galleries) . ' data galeri berhasil di-seed.');
    }
}
