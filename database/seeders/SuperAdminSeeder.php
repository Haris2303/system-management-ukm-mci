<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Buat akun Super Admin awal untuk akses panel.
     *
     * Default credentials:
     *   Email:    superadmin@mci.ac.id
     *   Password: superadmin123
     *
     * ⚠️ WAJIB: Ganti password ini setelah login pertama!
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@mci.ac.id'],
            [
                'name'              => 'Super Administrator',
                'email'             => 'superadmin@mci.ac.id',
                'password'          => Hash::make('superadmin123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign role super_admin
        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('✅ Super Admin berhasil dibuat!');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->warn('   📧 Email:    superadmin@mci.ac.id');
        $this->command->warn('   🔑 Password: superadmin123');
        $this->command->newLine();
        $this->command->error('   ⚠️  GANTI PASSWORD INI SETELAH LOGIN PERTAMA!');
        $this->command->info('═══════════════════════════════════════════════════');
    }
}
