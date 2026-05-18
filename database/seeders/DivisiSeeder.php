<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisis = [
            ['nama' => 'Programming',   'icon' => '💻', 'deskripsi' => 'Membangun aplikasi web modern dengan framework terkini seperti Laravel, React, dan Vue.', 'urut' => 1],
            ['nama' => 'Desain Grafis',       'icon' => '🎨', 'deskripsi' => 'Menciptakan pengalaman pengguna yang intuitif dan estetis menggunakan Figma.', 'urut' => 2],
            ['nama' => 'Cinematography',     'icon' => '📸', 'deskripsi' => 'Mempelajari keamanan sistem, ethical hacking, dan CTF competition.', 'urut' => 3],
            ['nama' => 'Game Developer',     'icon' => '🎮', 'deskripsi' => 'Mempelajari pembuatan karakter hingga gameplay pada game.', 'urut' => 4],
        ];

        foreach ($divisis as $data) {
            Divisi::firstOrCreate(
                ['nama' => $data['nama']],
                [...$data, 'is_active' => true]
            );
        }

        $this->command->info('✅ ' . count($divisis) . ' divisi berhasil di-seed.');
    }
}
