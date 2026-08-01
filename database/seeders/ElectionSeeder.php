<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ElectionSeeder extends Seeder
{
    public function run(): void
    {
        $ketua = User::role('ketua_ukm')->first();
        if (! $ketua) {
            $this->command->warn('⚠️  Tidak ada Ketua UKM. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Ambil kandidat dari anggota + ketua divisi agar bervariasi
        $calonUsers = User::role(['anggota', 'ketua_divisi'])->inRandomOrder()->limit(3)->get();
        if ($calonUsers->count() < 2) {
            $this->command->warn('⚠️  Butuh minimal 2 anggota untuk membuat kandidat pemilihan.');
            return;
        }

        // ── 1. Pemilihan yang sudah selesai (untuk demo hasil & rekap suara) ──
        $electionSelesai = Election::firstOrCreate(
            ['judul' => 'Pemilihan Ketua UKM MCI Periode 2025/2026'],
            [
                'deskripsi'       => 'Pemilihan ketua umum UKM MCI untuk periode kepengurusan 2025/2026 melalui e-voting anggota aktif.',
                'posisi'          => 'Ketua UKM',
                'waktu_mulai'     => Carbon::now()->subMonths(2),
                'waktu_selesai'   => Carbon::now()->subMonths(2)->addDays(3),
                'status'          => 'selesai',
                'is_anonim'       => true,
                'tampil_realtime' => true,
                'created_by'      => $ketua->id,
            ]
        );

        if ($electionSelesai->wasRecentlyCreated) {
            $candidates = collect();
            foreach ($calonUsers as $i => $user) {
                $candidates->push(Candidate::create([
                    'election_id' => $electionSelesai->id,
                    'user_id'     => $user->id,
                    'visi'        => 'Menjadikan UKM MCI sebagai wadah pengembangan talenta digital yang unggul dan solid.',
                    'misi'        => "Mengaktifkan pelatihan rutin tiap divisi.\nMeningkatkan transparansi keuangan organisasi.\nMemperluas jejaring kerja sama eksternal.",
                    'foto'        => null,
                    'urut'        => $i + 1,
                ]));
            }

            // Simulasikan suara masuk dari seluruh anggota aktif (kecuali super_admin & demisioner)
            $voters = User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'demisioner']))->get();
            foreach ($voters as $voter) {
                $candidate = $candidates->random();
                $hash = hash('sha256', $voter->id . '-' . $electionSelesai->id . '-ukm-mci-secret');

                Vote::firstOrCreate(
                    ['election_id' => $electionSelesai->id, 'voter_hash' => $hash],
                    ['candidate_id' => $candidate->id]
                );
            }
        }

        // ── 2. Pemilihan draft (belum berjalan) untuk demo pembuatan pemilihan baru ──
        $electionDraft = Election::firstOrCreate(
            ['judul' => 'Pemilihan Ketua Divisi Programming 2026'],
            [
                'deskripsi'       => 'Pemilihan ketua divisi Programming untuk periode selanjutnya.',
                'posisi'          => 'Ketua Divisi',
                'waktu_mulai'     => Carbon::now()->addWeeks(2),
                'waktu_selesai'   => Carbon::now()->addWeeks(2)->addDays(3),
                'status'          => 'draft',
                'is_anonim'       => true,
                'tampil_realtime' => false,
                'created_by'      => $ketua->id,
            ]
        );

        if ($electionDraft->wasRecentlyCreated) {
            foreach ($calonUsers->take(2) as $i => $user) {
                Candidate::firstOrCreate(
                    ['election_id' => $electionDraft->id, 'user_id' => $user->id],
                    ['visi' => 'Membawa divisi Programming lebih aktif berkompetisi.', 'misi' => 'Rutin mengadakan sharing session teknologi terbaru.', 'urut' => $i + 1]
                );
            }
        }

        $this->command->info('✅ Data pemilihan (election, kandidat, dan suara) berhasil di-seed.');
    }
}
