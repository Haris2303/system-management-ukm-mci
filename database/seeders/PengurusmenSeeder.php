<?php

namespace Database\Seeders;

use App\Models\Pengurusmen;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengurusmenSeeder extends Seeder
{
    /**
     * Struktur kepengurusan publik (halaman landing "Pengurus"), disinkronkan
     * dari data User + role yang sudah di-seed di UserSeeder.
     */
    public function run(): void
    {
        $mapping = [
            ['role' => 'ketua_ukm', 'jabatan' => 'Ketua UKM', 'urut' => 1],
            ['role' => 'bendahara', 'jabatan' => 'Bendahara', 'urut' => 2],
            ['role' => 'sekretaris', 'jabatan' => 'Sekretaris', 'urut' => 3],
            ['role' => 'ketua_divisi', 'jabatan' => 'Ketua Divisi Programming', 'urut' => 4],
        ];

        $urut = 0;
        foreach ($mapping as $item) {
            $user = User::role($item['role'])->first();
            if (! $user) {
                continue;
            }

            Pengurusmen::firstOrCreate(
                ['nama' => $user->name, 'jabatan' => $item['jabatan']],
                [
                    'divisi'    => $user->divisi?->nama,
                    'foto'      => null,
                    'angkatan'  => (string) fake()->numberBetween(2021, 2023),
                    'instagram' => null,
                    'linkedin'  => null,
                    'urut'      => $item['urut'],
                    'is_active' => true,
                ]
            );
            $urut = $item['urut'];
        }

        // Tambahkan anggota inti divisi lainnya sebagai staff pengurusmen
        User::role('anggota')->limit(6)->get()->each(function (User $user) use (&$urut) {
            $urut++;
            Pengurusmen::firstOrCreate(
                ['nama' => $user->name, 'jabatan' => 'Staff Divisi'],
                [
                    'divisi'    => $user->divisi?->nama,
                    'foto'      => null,
                    'angkatan'  => (string) fake()->numberBetween(2022, 2025),
                    'instagram' => null,
                    'linkedin'  => null,
                    'urut'      => $urut,
                    'is_active' => true,
                ]
            );
        });

        $this->command->info('✅ Data pengurusmen berhasil di-seed dari data User.');
    }
}
