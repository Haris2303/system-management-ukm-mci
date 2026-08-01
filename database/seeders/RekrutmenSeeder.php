<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\JawabanPendaftar;
use App\Models\Member;
use App\Models\OpenRecruitment;
use App\Models\Pendaftar;
use App\Models\PertanyaanSeleksi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RekrutmenSeeder extends Seeder
{
    public function run(): void
    {
        $divisis = Divisi::all();
        if ($divisis->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada divisi. Jalankan DivisiSeeder terlebih dahulu.');
            return;
        }

        // ── 1. Gelombang Open Recruitment ──
        OpenRecruitment::firstOrCreate(
            ['judul' => 'Open Recruitment Anggota Baru Semester Genap 2025/2026'],
            [
                'gelombang'     => 'Gelombang 1',
                'deskripsi'     => 'Pendaftaran anggota baru UKM MCI untuk semester genap tahun akademik 2025/2026. Tersedia 4 divisi dengan fokus keahlian masing-masing.',
                'waktu_mulai'   => Carbon::now()->subWeeks(2),
                'waktu_selesai' => Carbon::now()->addWeeks(1),
                'is_active'     => true,
                'catatan'       => 'Wajib melampirkan motivasi bergabung minimal 100 kata.',
            ]
        );

        OpenRecruitment::firstOrCreate(
            ['judul' => 'Open Recruitment Anggota Baru Semester Ganjil 2024/2025'],
            [
                'gelombang'     => 'Gelombang 1',
                'deskripsi'     => 'Pendaftaran anggota baru periode sebelumnya (arsip).',
                'waktu_mulai'   => Carbon::now()->subMonths(8),
                'waktu_selesai' => Carbon::now()->subMonths(7),
                'is_active'     => false,
                'catatan'       => null,
            ]
        );

        // ── 2. Pertanyaan Seleksi per Divisi ──
        $pertanyaanPerDivisi = [
            'Programming'    => ['Bahasa pemrograman apa yang paling kamu kuasai dan mengapa?', 'Ceritakan pengalamanmu membuat sebuah project (jika ada).'],
            'Desain Grafis'  => ['Apa software desain yang biasa kamu gunakan?', 'Bagikan 1 karya desain terbaikmu dan ceritakan prosesnya.'],
            'Cinematography' => ['Apakah kamu punya pengalaman mengoperasikan kamera DSLR/mirrorless?', 'Genre video seperti apa yang paling kamu minati?'],
            'Game Developer' => ['Engine game apa yang pernah kamu coba (Unity/Godot/lainnya)?', 'Ceritakan ide game sederhana yang ingin kamu buat.'],
        ];

        foreach ($pertanyaanPerDivisi as $namaDivisi => $pertanyaanList) {
            $divisi = $divisis->firstWhere('nama', $namaDivisi);
            if (! $divisi) {
                continue;
            }

            foreach ($pertanyaanList as $urut => $teks) {
                PertanyaanSeleksi::firstOrCreate(
                    ['divisi_id' => $divisi->id, 'pertanyaan_teks' => $teks],
                    ['is_active' => true, 'urut' => $urut + 1]
                );
            }
        }

        // ── 3. Pendaftar (calon anggota baru) + jawaban seleksi ──
        $namaCalon = [
            ['nama' => 'Aditya Rahman', 'divisi' => 'Programming', 'status' => 'lulus'],
            ['nama' => 'Bunga Lestari', 'divisi' => 'Programming', 'status' => 'menunggu'],
            ['nama' => 'Citra Ayu Ningsih', 'divisi' => 'Desain Grafis', 'status' => 'lulus'],
            ['nama' => 'Dimas Prasetyo', 'divisi' => 'Desain Grafis', 'status' => 'ditolak'],
            ['nama' => 'Erlangga Saputra', 'divisi' => 'Cinematography', 'status' => 'menunggu'],
            ['nama' => 'Fitriani Anggraini', 'divisi' => 'Cinematography', 'status' => 'lulus'],
            ['nama' => 'Galih Wicaksono', 'divisi' => 'Game Developer', 'status' => 'menunggu'],
            ['nama' => 'Hana Salsabila', 'divisi' => 'Game Developer', 'status' => 'lulus'],
        ];

        $totalJawaban = 0;
        foreach ($namaCalon as $i => $calon) {
            $divisi = $divisis->firstWhere('nama', $calon['divisi']);
            if (! $divisi) {
                continue;
            }

            $nim = '20' . str_pad((string) (240100 + $i), 9, '0', STR_PAD_LEFT);

            $pendaftar = Pendaftar::firstOrCreate(
                ['nim' => $nim, 'divisi_id' => $divisi->id],
                [
                    'nama'     => $calon['nama'],
                    'email'    => strtolower(str_replace(' ', '.', $calon['nama'])) . '@student.ac.id',
                    'no_hp'    => '08' . fake()->numerify('##########'),
                    'angkatan' => '2025',
                    'status'   => $calon['status'],
                ]
            );

            if ($pendaftar->wasRecentlyCreated) {
                foreach ($divisi->pertanyaanSeleksis as $pertanyaan) {
                    JawabanPendaftar::create([
                        'pendaftar_id'  => $pendaftar->id,
                        'pertanyaan_id' => $pertanyaan->id,
                        'jawaban_teks'  => fake()->paragraph(3),
                        'nilai_skor'    => $calon['status'] === 'menunggu' ? null : fake()->numberBetween(60, 95),
                    ]);
                    $totalJawaban++;
                }
            }
        }

        // ── 4. Member (alur pendaftaran lama, untuk kelengkapan data historis) ──
        if (Member::count() === 0) {
            Member::factory()->count(10)->create();
        }

        $this->command->info('✅ Data rekrutmen (open recruitment, pertanyaan seleksi, pendaftar, jawaban, member) berhasil di-seed.');
    }
}
