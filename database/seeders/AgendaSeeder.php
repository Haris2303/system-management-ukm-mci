<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'demisioner']))->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada user. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $agendas = [
            ['nama' => 'Rapat Rutin Bulanan - Januari', 'mulai' => Carbon::now()->subMonths(6)->setTime(15, 0), 'lokasi' => 'Sekretariat UKM MCI', 'lampau' => true],
            ['nama' => 'Briefing Program Kerja Semester Genap', 'mulai' => Carbon::now()->subMonths(5)->setTime(16, 0), 'lokasi' => 'Ruang Seminar Gedung B', 'lampau' => true],
            ['nama' => 'Kumpul Anggota Aktif', 'mulai' => Carbon::now()->subMonths(3)->setTime(15, 30), 'lokasi' => 'Aula Kampus', 'lampau' => true],
            ['nama' => 'Rapat Koordinasi Divisi Programming', 'mulai' => Carbon::now()->subMonth()->setTime(14, 0), 'lokasi' => 'Lab Komputer 2', 'lampau' => true],
            ['nama' => 'Evaluasi Kegiatan Semester', 'mulai' => Carbon::now()->subWeeks(2)->setTime(15, 0), 'lokasi' => 'Sekretariat UKM MCI', 'lampau' => true],
            ['nama' => 'Rapat Persiapan Pemilihan Ketua Baru', 'mulai' => Carbon::now()->addDays(5)->setTime(15, 0), 'lokasi' => 'Ruang Seminar Gedung B', 'lampau' => false],
        ];

        $totalPresensi = 0;

        foreach ($agendas as $data) {
            $waktuMulai = $data['mulai'];
            $waktuSelesai = (clone $waktuMulai)->addHours(2);

            $agenda = Agenda::firstOrCreate(
                ['nama_agenda' => $data['nama'], 'waktu_mulai' => $waktuMulai],
                [
                    'deskripsi'     => 'Agenda ' . $data['nama'] . ' bagi seluruh pengurus dan anggota aktif UKM MCI.',
                    'waktu_selesai' => $waktuSelesai,
                    'lokasi'        => $data['lokasi'],
                    'is_active'     => ! $data['lampau'],
                    'qr_code_token' => Str::random(32),
                ]
            );

            if (! $agenda->wasRecentlyCreated) {
                continue;
            }

            if ($data['lampau']) {
                foreach ($users as $user) {
                    $status = fake()->randomElement(['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Izin', 'Absen']);

                    Presensi::create([
                        'user_id'   => $user->id,
                        'agenda_id' => $agenda->id,
                        'status'    => $status,
                        'jam_hadir' => $status === 'Absen' ? $waktuSelesai : (clone $waktuMulai)->addMinutes(fake()->numberBetween(0, 30)),
                    ]);
                    $totalPresensi++;
                }
            }
        }

        $this->command->info("✅ Berhasil membuat " . count($agendas) . " agenda dengan {$totalPresensi} data presensi.");
    }
}
