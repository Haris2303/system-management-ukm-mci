<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\ProgramKerja;
use App\Models\TugasProker;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgramKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $pic = [
            'ketua'    => User::where('email', 'ketua@mci.ac.id')->first(),
            'kadiv'    => User::where('email', 'kadiv.prog@mci.com')->first(),
            'bendahara'=> User::where('email', 'bendahara@mci.ac.id')->first(),
            'anggota'  => User::where('email', 'anggota1@mci.ac.com')->first(),
        ];

        // Fallback: pakai user pertama yang ada jika belum di-seed
        $fallback = User::first();
        foreach ($pic as $key => $user) {
            $pic[$key] = $user ?? $fallback;
        }

        if (! $fallback) {
            $this->command->warn('⚠️  Tidak ada user. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $data = [

            // ── PROGRAMMING ──────────────────────────────────────────────
            'Programming' => [
                [
                    'proker' => [
                        'nama_proker'     => 'Workshop Laravel untuk Anggota Baru',
                        'deskripsi'       => 'Workshop dua hari memperkenalkan framework Laravel kepada anggota baru divisi Programming, mencakup routing, Eloquent ORM, dan Blade templating.',
                        'pic'             => 'kadiv',
                        'tanggal_mulai'   => now()->subDays(30),
                        'tanggal_selesai' => now()->addDays(7),
                        'status'          => 'active',
                    ],
                    'tugas' => [
                        ['nama' => 'Persiapan materi dan slide presentasi',  'selesai' => true],
                        ['nama' => 'Setup environment dan repo GitHub',       'selesai' => true],
                        ['nama' => 'Pelaksanaan workshop hari pertama',       'selesai' => true],
                        ['nama' => 'Pelaksanaan workshop hari kedua',         'selesai' => false],
                        ['nama' => 'Evaluasi dan pengumpulan feedback',       'selesai' => false],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Pengembangan Sistem Absensi QR Code',
                        'deskripsi'       => 'Membangun sistem absensi digital berbasis QR Code untuk kegiatan internal kampus menggunakan Laravel dan Vue.js.',
                        'pic'             => 'kadiv',
                        'tanggal_mulai'   => now()->subDays(45),
                        'tanggal_selesai' => now()->addDays(20),
                        'status'          => 'active',
                    ],
                    'tugas' => [
                        ['nama' => 'Perancangan sistem dan ERD database',     'selesai' => true],
                        ['nama' => 'Setup project Laravel + Vue.js',          'selesai' => true],
                        ['nama' => 'Implementasi generate QR Code per sesi',  'selesai' => false],
                        ['nama' => 'Implementasi scan dan validasi QR',       'selesai' => false],
                        ['nama' => 'Dashboard monitoring kehadiran',          'selesai' => false],
                        ['nama' => 'Testing dan deployment ke server',        'selesai' => false],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Pembuatan Website Profil UKM MCI',
                        'deskripsi'       => 'Merancang dan mengembangkan website resmi UKM MCI yang modern, responsif, dan dilengkapi sistem administrasi digital berbasis Filament PHP.',
                        'pic'             => 'kadiv',
                        'tanggal_mulai'   => now()->subDays(90),
                        'tanggal_selesai' => now()->subDays(10),
                        'status'          => 'completed',
                    ],
                    'tugas' => [
                        ['nama' => 'Analisis kebutuhan dan wireframing',      'selesai' => true],
                        ['nama' => 'Desain UI/UX mockup di Figma',            'selesai' => true],
                        ['nama' => 'Pengembangan frontend (Blade + Tailwind)','selesai' => true],
                        ['nama' => 'Pengembangan backend (Laravel + Filament)','selesai' => true],
                        ['nama' => 'Integrasi chatbot AI berbasis RAG',       'selesai' => true],
                        ['nama' => 'Testing, bug fixing, dan deployment',     'selesai' => true],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Hackathon Internal UKM MCI 2025',
                        'deskripsi'       => 'Kompetisi 24 jam pembuatan aplikasi untuk seluruh anggota UKM MCI dengan hadiah total senilai Rp 5.000.000.',
                        'pic'             => 'ketua',
                        'tanggal_mulai'   => now()->addDays(14),
                        'tanggal_selesai' => now()->addDays(60),
                        'status'          => 'planning',
                    ],
                    'tugas' => [
                        ['nama' => 'Pembentukan panitia pelaksana',           'selesai' => false],
                        ['nama' => 'Penentuan tema dan peraturan lomba',      'selesai' => false],
                        ['nama' => 'Pencarian sponsor dan hadiah',            'selesai' => false],
                        ['nama' => 'Registrasi dan pembentukan tim peserta',  'selesai' => false],
                        ['nama' => 'Pelaksanaan hackathon',                   'selesai' => false],
                        ['nama' => 'Penjurian dan pengumuman pemenang',       'selesai' => false],
                    ],
                ],
            ],

            // ── DESAIN GRAFIS ────────────────────────────────────────────
            'Desain Grafis' => [
                [
                    'proker' => [
                        'nama_proker'     => 'Pembuatan Identitas Visual UKM MCI 2025',
                        'deskripsi'       => 'Merancang ulang logo, palet warna, tipografi, dan panduan brand UKM MCI untuk digunakan di seluruh media komunikasi organisasi.',
                        'pic'             => 'anggota',
                        'tanggal_mulai'   => now()->subDays(60),
                        'tanggal_selesai' => now()->subDays(15),
                        'status'          => 'completed',
                    ],
                    'tugas' => [
                        ['nama' => 'Riset tren desain dan mood board',        'selesai' => true],
                        ['nama' => 'Pembuatan 3 alternatif konsep logo',      'selesai' => true],
                        ['nama' => 'Presentasi dan voting pemilihan konsep',  'selesai' => true],
                        ['nama' => 'Finalisasi logo dan variasi warna',       'selesai' => true],
                        ['nama' => 'Penyusunan brand guideline document',     'selesai' => true],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Desain Merchandise dan Atribut UKM',
                        'deskripsi'       => 'Merancang dan memproduksi merchandise resmi UKM MCI berupa kaos, tote bag, dan stiker untuk anggota aktif.',
                        'pic'             => 'anggota',
                        'tanggal_mulai'   => now()->subDays(20),
                        'tanggal_selesai' => now()->addDays(25),
                        'status'          => 'active',
                    ],
                    'tugas' => [
                        ['nama' => 'Survei minat dan ukuran kepada anggota',  'selesai' => true],
                        ['nama' => 'Pembuatan desain mockup kaos dan tote bag','selesai' => true],
                        ['nama' => 'Revisi desain berdasarkan feedback',      'selesai' => false],
                        ['nama' => 'Pencarian dan negosiasi dengan vendor',   'selesai' => false],
                        ['nama' => 'Proses produksi merchandise',             'selesai' => false],
                        ['nama' => 'Distribusi kepada anggota',               'selesai' => false],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Workshop Figma untuk Anggota Baru',
                        'deskripsi'       => 'Pelatihan intensif penggunaan Figma untuk desain UI/UX, mencakup komponen, auto-layout, prototyping, dan handoff ke developer.',
                        'pic'             => 'anggota',
                        'tanggal_mulai'   => now()->addDays(10),
                        'tanggal_selesai' => now()->addDays(45),
                        'status'          => 'planning',
                    ],
                    'tugas' => [
                        ['nama' => 'Penyusunan kurikulum dan modul workshop', 'selesai' => false],
                        ['nama' => 'Pembuatan project template Figma',        'selesai' => false],
                        ['nama' => 'Pelaksanaan workshop sesi 1 (dasar)',     'selesai' => false],
                        ['nama' => 'Pelaksanaan workshop sesi 2 (lanjutan)',  'selesai' => false],
                        ['nama' => 'Tugas akhir: desain landing page',        'selesai' => false],
                    ],
                ],
            ],

            // ── CINEMATOGRAPHY ───────────────────────────────────────────
            'Cinematography' => [
                [
                    'proker' => [
                        'nama_proker'     => 'Pembuatan Video Profil Resmi UKM MCI',
                        'deskripsi'       => 'Memproduksi video profil berdurasi 3–5 menit yang merepresentasikan visi, misi, dan kegiatan UKM MCI untuk keperluan promosi dan rekrutmen.',
                        'pic'             => 'ketua',
                        'tanggal_mulai'   => now()->subDays(35),
                        'tanggal_selesai' => now()->addDays(14),
                        'status'          => 'active',
                    ],
                    'tugas' => [
                        ['nama' => 'Penulisan skrip dan storyboard',          'selesai' => true],
                        ['nama' => 'Hunting dan persiapan lokasi syuting',    'selesai' => true],
                        ['nama' => 'Proses syuting utama',                    'selesai' => true],
                        ['nama' => 'Editing kasar (rough cut)',               'selesai' => true],
                        ['nama' => 'Color grading dan penambahan musik',      'selesai' => false],
                        ['nama' => 'Review final dan upload ke YouTube',      'selesai' => false],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Dokumentasi Kegiatan Semesteran UKM',
                        'deskripsi'       => 'Mendokumentasikan seluruh kegiatan UKM MCI semester ini dalam bentuk foto dan video highlight untuk arsip organisasi dan konten media sosial.',
                        'pic'             => 'ketua',
                        'tanggal_mulai'   => now()->subDays(10),
                        'tanggal_selesai' => now()->addDays(75),
                        'status'          => 'active',
                    ],
                    'tugas' => [
                        ['nama' => 'Pembuatan jadwal kegiatan yang perlu didokumentasi','selesai' => true],
                        ['nama' => 'Dokumentasi Workshop Flutter (Maret)',    'selesai' => true],
                        ['nama' => 'Dokumentasi Workshop Public Speaking',    'selesai' => false],
                        ['nama' => 'Dokumentasi Hackathon Internal',          'selesai' => false],
                        ['nama' => 'Penyusunan highlight video akhir semester','selesai' => false],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Workshop Dasar Fotografi dan Videografi',
                        'deskripsi'       => 'Pelatihan dasar penggunaan kamera DSLR, komposisi foto, pencahayaan, dan teknik videografi dasar untuk anggota baru divisi Cinematography.',
                        'pic'             => 'ketua',
                        'tanggal_mulai'   => now()->addDays(20),
                        'tanggal_selesai' => now()->addDays(55),
                        'status'          => 'planning',
                    ],
                    'tugas' => [
                        ['nama' => 'Persiapan peralatan kamera dan lighting',  'selesai' => false],
                        ['nama' => 'Penyusunan materi teori fotografi',        'selesai' => false],
                        ['nama' => 'Sesi praktik foto indoor',                 'selesai' => false],
                        ['nama' => 'Sesi praktik foto dan video outdoor',      'selesai' => false],
                        ['nama' => 'Pameran karya foto peserta workshop',      'selesai' => false],
                    ],
                ],
            ],

            // ── GAME DEVELOPER ───────────────────────────────────────────
            'Game Developer' => [
                [
                    'proker' => [
                        'nama_proker'     => 'Game Jam Internal UKM MCI 2025',
                        'deskripsi'       => 'Kompetisi pembuatan game dalam 48 jam menggunakan Unity atau Godot dengan tema yang diumumkan saat event dimulai.',
                        'pic'             => 'bendahara',
                        'tanggal_mulai'   => now()->subDays(50),
                        'tanggal_selesai' => now()->subDays(5),
                        'status'          => 'completed',
                    ],
                    'tugas' => [
                        ['nama' => 'Penentuan format dan aturan game jam',    'selesai' => true],
                        ['nama' => 'Pembukaan registrasi dan pembentukan tim','selesai' => true],
                        ['nama' => 'Pelaksanaan game jam 48 jam',             'selesai' => true],
                        ['nama' => 'Proses penjurian oleh mentor',            'selesai' => true],
                        ['nama' => 'Pengumuman pemenang dan showcase game',   'selesai' => true],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Workshop Unity untuk Anggota Baru',
                        'deskripsi'       => 'Pelatihan membuat game 2D sederhana menggunakan Unity mulai dari pembuatan scene, physics, animasi, hingga build ke Android.',
                        'pic'             => 'bendahara',
                        'tanggal_mulai'   => now()->subDays(15),
                        'tanggal_selesai' => now()->addDays(30),
                        'status'          => 'active',
                    ],
                    'tugas' => [
                        ['nama' => 'Instalasi Unity dan setup project',       'selesai' => true],
                        ['nama' => 'Sesi 1: Pengenalan UI dan scene editor',  'selesai' => true],
                        ['nama' => 'Sesi 2: Physics dan collision detection', 'selesai' => false],
                        ['nama' => 'Sesi 3: Animasi karakter 2D',             'selesai' => false],
                        ['nama' => 'Sesi 4: UI game dan scoring system',      'selesai' => false],
                        ['nama' => 'Tugas akhir: build game ke Android',      'selesai' => false],
                    ],
                ],
                [
                    'proker' => [
                        'nama_proker'     => 'Kompetisi Game Design Antar Divisi',
                        'deskripsi'       => 'Turnamen internal antaranggota semua divisi untuk merancang konsep game terbaik, meliputi game design document, prototipe, dan presentasi.',
                        'pic'             => 'ketua',
                        'tanggal_mulai'   => now()->addDays(30),
                        'tanggal_selesai' => now()->addDays(80),
                        'status'          => 'planning',
                    ],
                    'tugas' => [
                        ['nama' => 'Sosialisasi dan pendaftaran tim',         'selesai' => false],
                        ['nama' => 'Workshop penulisan Game Design Document', 'selesai' => false],
                        ['nama' => 'Pengumpulan GDD dan prototipe awal',      'selesai' => false],
                        ['nama' => 'Sesi presentasi dan penilaian juri',      'selesai' => false],
                        ['nama' => 'Pengumuman pemenang dan dokumentasi',     'selesai' => false],
                    ],
                ],
            ],
        ];

        $totalProker = 0;
        $totalTugas  = 0;

        foreach ($data as $namaDivisi => $prokers) {
            $divisi = Divisi::where('nama', $namaDivisi)->first();

            if (! $divisi) {
                $this->command->warn("⚠️  Divisi [{$namaDivisi}] tidak ditemukan. Lewati.");
                continue;
            }

            foreach ($prokers as $item) {
                $pd = $item['proker'];

                $proker = ProgramKerja::firstOrCreate(
                    ['divisi_id' => $divisi->id, 'nama_proker' => $pd['nama_proker']],
                    [
                        'deskripsi'       => $pd['deskripsi'],
                        'pic_id'          => $pic[$pd['pic']]?->id ?? $fallback->id,
                        'tanggal_mulai'   => $pd['tanggal_mulai'],
                        'tanggal_selesai' => $pd['tanggal_selesai'],
                        'status'          => $pd['status'],
                        'progress_persen' => 0,
                    ]
                );

                // Buat tugas hanya jika proker baru dibuat
                if ($proker->wasRecentlyCreated) {
                    foreach ($item['tugas'] as $urut => $tugas) {
                        TugasProker::create([
                            'proker_id'  => $proker->id,
                            'nama_tugas' => $tugas['nama'],
                            'is_selesai' => $tugas['selesai'],
                            'urut'       => $urut + 1,
                        ]);
                        $totalTugas++;
                    }

                    // Hitung progress dari tugas yang dibuat
                    $proker->updateProgress();
                    $totalProker++;
                }
            }
        }

        $this->command->info("✅ Berhasil membuat {$totalProker} program kerja dengan {$totalTugas} tugas.");
    }
}
