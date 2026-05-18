<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil contoh divisi untuk keperluan testing
        $divisiProgramming = Divisi::where('nama', 'Programming')->first();
        $divisiDesain = Divisi::where('nama', 'Desain Grafis')->first();

        // 2. Ketua UKM
        $ketua = User::firstOrCreate(
            ['email' => 'ketua@mci.ac.id'],
            [
                'name' => 'Budi Ketua',
                'password' => Hash::make('password'),
                'no_hp' => '082222222222',
                'divisi_id' => null,
            ]
        );
        $ketua->assignRole('ketua_ukm');

        // 3. Bendahara (Biasanya masuk divisi tertentu atau netral)
        $bendahara = User::firstOrCreate(
            ['email' => 'bendahara@mci.ac.id'],
            [
                'name' => 'Siti Bendahara',
                'password' => Hash::make('password'),
                'no_hp' => '083333333333',
                'divisi_id' => $divisiProgramming?->id,
            ]
        );
        $bendahara->assignRole('bendahara');

        // 4. Ketua Divisi (Contoh: Ketua Programming)
        $ketuaDiv = User::firstOrCreate(
            ['email' => 'kadiv.prog@mci.com'],
            [
                'name' => 'Andi Kadiv',
                'password' => Hash::make('password'),
                'no_hp' => '084444444444',
                'divisi_id' => $divisiProgramming?->id,
            ]
        );
        $ketuaDiv->assignRole('ketua_divisi');

        // 5. Anggota (Contoh: Anggota Desain)
        $anggota = User::firstOrCreate(
            ['email' => 'anggota1@mci.ac.com'],
            [
                'name' => 'Rizky Anggota',
                'password' => Hash::make('password'),
                'no_hp' => '085555555555',
                'divisi_id' => $divisiDesain?->id,
            ]
        );
        $anggota->assignRole('anggota');

        $this->command->info('✅ Berhasil membuat User contoh untuk setiap Role.');
    }
}
