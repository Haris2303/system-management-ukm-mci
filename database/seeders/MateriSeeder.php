<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        $uploader = User::role('ketua_divisi')->first() ?? User::first();
        if (! $uploader) {
            $this->command->warn('⚠️  Tidak ada user. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $materiUmum = [
            ['judul' => 'Panduan Etika Berorganisasi di UKM MCI', 'link' => null],
            ['judul' => 'Template Laporan Pertanggungjawaban Proker', 'link' => null],
            ['judul' => 'Rekaman Onboarding Anggota Baru', 'link' => 'https://youtube.com/watch?v=demo-onboarding'],
        ];

        foreach ($materiUmum as $data) {
            Materi::firstOrCreate(
                ['judul' => $data['judul']],
                [
                    'deskripsi'   => 'Materi umum untuk seluruh anggota UKM MCI.',
                    'file_path'   => null,
                    'link_url'    => $data['link'],
                    'divisi_id'   => null,
                    'uploaded_by' => $uploader->id,
                ]
            );
        }

        $materiDivisi = [
            'Programming'    => ['Dasar Laravel & Eloquent ORM', 'Pengenalan Git & GitHub Workflow'],
            'Desain Grafis'  => ['Fundamental Desain UI/UX dengan Figma', 'Prinsip Dasar Tipografi & Warna'],
            'Cinematography' => ['Teknik Dasar Cinematography', 'Editing Video dengan Adobe Premiere Pro'],
            'Game Developer' => ['Pengenalan Unity Engine', 'Dasar Game Design Document'],
        ];

        $total = 0;
        foreach ($materiDivisi as $namaDivisi => $judulList) {
            $divisi = Divisi::where('nama', $namaDivisi)->first();
            if (! $divisi) {
                continue;
            }

            foreach ($judulList as $judul) {
                Materi::firstOrCreate(
                    ['judul' => $judul],
                    [
                        'deskripsi'   => "Materi pelatihan khusus divisi {$namaDivisi}.",
                        'file_path'   => null,
                        'link_url'    => null,
                        'divisi_id'   => $divisi->id,
                        'uploaded_by' => $uploader->id,
                    ]
                );
                $total++;
            }
        }

        $this->command->info('✅ ' . (count($materiUmum) + $total) . ' materi berhasil di-seed.');
    }
}
