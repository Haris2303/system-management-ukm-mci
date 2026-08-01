<?php

namespace Database\Seeders;

use App\Models\ProfilUkm;
use Illuminate\Database\Seeder;

class ProfilUkmSeeder extends Seeder
{
    public function run(): void
    {
        ProfilUkm::firstOrCreate(
            ['nama_ukm' => 'UKM MCI'],
            [
                'tagline'    => 'Media Creative Informations — Berkarya, Berkolaborasi, Berinovasi',
                'deskripsi'  => 'UKM Media Creative Informations (MCI) adalah unit kegiatan mahasiswa yang mewadahi minat dan bakat mahasiswa di bidang teknologi informasi, desain grafis, sinematografi, dan pengembangan game.',
                'visi'       => 'Menjadi wadah pengembangan kreativitas dan kompetensi mahasiswa di bidang teknologi informasi yang unggul, kolaboratif, dan berdaya saing.',
                'misi'       => "Menyelenggarakan pelatihan dan workshop rutin di setiap divisi.\nMemfasilitasi anggota untuk berkompetisi di ajang tingkat nasional.\nMembangun jejaring kerja sama dengan komunitas dan industri teknologi.\nMengembangkan sistem administrasi organisasi berbasis digital.",
                'keunggulan' => [
                    ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Empat divisi keahlian: Programming, Desain Grafis, Cinematography, Game Developer'],
                    ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Sistem administrasi digital terintegrasi (E-Kas, E-Voting, Presensi QR)'],
                    ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Pembinaan rutin melalui workshop dan mentoring divisi'],
                    ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Jejaring alumni dan kolaborasi lintas UKM'],
                ],
            ]
        );

        $this->command->info('✅ Profil UKM berhasil di-seed.');
    }
}
