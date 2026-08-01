<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            DivisiSeeder::class,
            UserSeeder::class,
            ProfilUkmSeeder::class,
            PengurusmenSeeder::class,
            ProgramKerjaSeeder::class,
            PostSeeder::class,
            GallerySeeder::class,
            AgendaSeeder::class,
            KasSeeder::class,
            MateriSeeder::class,
            RekrutmenSeeder::class,
            ElectionSeeder::class,
        ]);
    }
}
