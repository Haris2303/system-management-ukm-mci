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
        // // Ambil contoh divisi untuk keperluan testing
        // $divisiProgramming = Divisi::where('nama', 'Programming')->first();
        // $divisiDesain = Divisi::where('nama', 'Desain Grafis')->first();

        // // 2. Ketua UKM
        // $ketua = User::firstOrCreate(
        //     ['email' => 'ketua@mci.ac.id'],
        //     [
        //         'name' => 'Budi Ketua',
        //         'password' => Hash::make('password'),
        //         'no_hp' => '082222222222',
        //         'divisi_id' => null,
        //     ]
        // );
        // $ketua->assignRole('ketua_ukm');

        // // 3. Bendahara (Biasanya masuk divisi tertentu atau netral)
        // $bendahara = User::firstOrCreate(
        //     ['email' => 'bendahara@mci.ac.id'],
        //     [
        //         'name' => 'Siti Bendahara',
        //         'password' => Hash::make('password'),
        //         'no_hp' => '083333333333',
        //         'divisi_id' => $divisiProgramming?->id,
        //     ]
        // );
        // $bendahara->assignRole('bendahara');

        // // 4. Ketua Divisi (Contoh: Ketua Programming)
        // $ketuaDiv = User::firstOrCreate(
        //     ['email' => 'kadiv.prog@mci.com'],
        //     [
        //         'name' => 'Andi Kadiv',
        //         'password' => Hash::make('password'),
        //         'no_hp' => '084444444444',
        //         'divisi_id' => $divisiProgramming?->id,
        //     ]
        // );
        // $ketuaDiv->assignRole('ketua_divisi');

        // // 5. Anggota (Contoh: Anggota Desain)
        // $anggota = User::firstOrCreate(
        //     ['email' => 'anggota1@mci.ac.com'],
        //     [
        //         'name' => 'Rizky Anggota',
        //         'password' => Hash::make('password'),
        //         'no_hp' => '085555555555',
        //         'divisi_id' => $divisiDesain?->id,
        //     ]
        // );
        // $anggota->assignRole('anggota');

        // $this->command->info('✅ Berhasil membuat User contoh untuk setiap Role.');



        // Ambil divisi yang dipakai (sesuai isi data Excel)
        $divisiProgramming     = Divisi::where('nama', 'Programming')->first();
        $divisiGameDeveloper   = Divisi::where('nama', 'Game Developer')->first();
        $divisiCinematography  = Divisi::where('nama', 'Cinematography')->first();

        // 1. Ketua UKM
        $ketua = User::firstOrCreate(
            ['email' => 'yayanrasya2@gmail.com'],
            [
                'name' => 'Yayan Rasya Aditya Malawat',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiCinematography?->id,
            ]
        );
        $ketua->assignRole('ketua_ukm');

        // 2. Bendahara
        $bendahara = User::firstOrCreate(
            ['email' => 'astielitaa87@gmail.com'],
            [
                'name' => 'Asti Elita Saraswati',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiProgramming?->id,
            ]
        );
        $bendahara->assignRole('bendahara');

        // 3. Ketua Divisi (Programming)
        $ketuaDiv = User::firstOrCreate(
            ['email' => 'khairyariqa@gmail.com'],
            [
                'name' => 'Khairy Ariqa',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiProgramming?->id,
            ]
        );
        $ketuaDiv->assignRole('ketua_divisi');

        // 4. Anggota - Cinematography
        $anggota1 = User::firstOrCreate(
            ['email' => 'mieshellada53@gmail.com'],
            [
                'name' => 'Mieshell Telling Ada',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiCinematography?->id,
            ]
        );
        $anggota1->assignRole('anggota');

        $anggota2 = User::firstOrCreate(
            ['email' => 'ritsuu0058@gmail.com'],
            [
                'name' => 'Risani Anggraeni',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiCinematography?->id,
            ]
        );
        $anggota2->assignRole('anggota');

        $anggota3 = User::firstOrCreate(
            ['email' => 'arthurmenanti31@gmail.con'],
            [
                'name' => 'Smith Arthur Menanti',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiCinematography?->id,
            ]
        );
        $anggota3->assignRole('anggota');

        // 5. Anggota - Game Developer
        $anggota4 = User::firstOrCreate(
            ['email' => 'dewinurlatifah26@gmail.com'],
            [
                'name' => 'Dewi Nur Latifah',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiGameDeveloper?->id,
            ]
        );
        $anggota4->assignRole('anggota');

        $anggota5 = User::firstOrCreate(
            ['email' => 'rizkysulaiman337@gmail.com'],
            [
                'name' => 'Muh. Rizky S',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiGameDeveloper?->id,
            ]
        );
        $anggota5->assignRole('anggota');

        $anggota6 = User::firstOrCreate(
            ['email' => 'Iy.amunau@gmail.com'],
            [
                'name' => 'Israel Yoeman Amunau',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiGameDeveloper?->id,
            ]
        );
        $anggota6->assignRole('anggota');

        // 6. Anggota - Programming
        $anggota7 = User::firstOrCreate(
            ['email' => 'nayyzhaff@gmail.com'],
            [
                'name' => 'Nayla Zhafrani Chairunisa',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiProgramming?->id,
            ]
        );
        $anggota7->assignRole('anggota');

        $anggota8 = User::firstOrCreate(
            ['email' => 'sititusyek@gmail.com'],
            [
                'name' => 'Siti Nurul Qomaria Tusyek',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiProgramming?->id,
            ]
        );
        $anggota8->assignRole('anggota');

        $anggota9 = User::firstOrCreate(
            ['email' => 'hayuningpratiwi21@gmail.com'],
            [
                'name' => 'Hayuning Diah Pratiwi',
                'password' => Hash::make('password'),
                'no_hp' => '',
                'divisi_id' => $divisiProgramming?->id,
            ]
        );
        $anggota9->assignRole('anggota');

        // 7. Sekretaris (demo role — belum ada di data Excel)
        $sekretaris = User::firstOrCreate(
            ['email' => 'sekretaris.mci@gmail.com'],
            [
                'name' => 'Nadia Putri Sekretaris',
                'password' => Hash::make('password'),
                'no_hp' => '086666666666',
                'divisi_id' => $divisiCinematography?->id,
                'periode' => '2025/2026',
            ]
        );
        $sekretaris->assignRole('sekretaris');

        // 8. Demisioner (contoh akun alumni/nonaktif untuk demo)
        $demisioner = User::firstOrCreate(
            ['email' => 'alumni.demisioner@gmail.com'],
            [
                'name' => 'Fajar Nugroho (Alumni)',
                'password' => Hash::make('password'),
                'no_hp' => '087777777777',
                'divisi_id' => $divisiProgramming?->id,
                'periode' => '2023/2024',
            ]
        );
        $demisioner->assignRole('demisioner');

        // Set periode aktif untuk seluruh pengurus & anggota inti di atas
        foreach ([$ketua, $bendahara, $ketuaDiv, $anggota1, $anggota2, $anggota3, $anggota4, $anggota5, $anggota6, $anggota7, $anggota8, $anggota9] as $u) {
            if (blank($u->periode)) {
                $u->update(['periode' => '2025/2026']);
            }
        }

        $this->command->info('✅ Berhasil membuat User dari data Excel untuk setiap Role.');

        // ── Tambahan anggota dummy (via factory) agar data lebih ramai saat presentasi ──
        $divisiList = collect([$divisiProgramming, $divisiGameDeveloper, $divisiCinematography])
            ->filter()
            ->values();

        if ($divisiList->isNotEmpty() && User::role('anggota')->count() < 20) {
            User::factory()
                ->count(12)
                ->create()
                ->each(function (User $user) use ($divisiList) {
                    $user->update([
                        'divisi_id' => $divisiList->random()->id,
                        'periode'   => '2025/2026',
                        'no_hp'     => '08' . fake()->numerify('##########'),
                    ]);
                    $user->assignRole('anggota');
                });

            $this->command->info('✅ 12 anggota dummy tambahan berhasil dibuat via factory.');
        }
    }
}
