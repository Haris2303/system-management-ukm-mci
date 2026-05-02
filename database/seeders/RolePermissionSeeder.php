<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Definisi 6 role di sistem UKM MCI:
     *
     * 1. super_admin   — Akses penuh ke semua fitur
     * 2. ketua_ukm     — Kelola seluruh organisasi (kecuali user/role)
     * 3. sekretaris    — Kelola berita, agenda, presensi, materi
     * 4. bendahara     — Kelola keuangan (e-kas)
     * 5. ketua_divisi  — Kelola pendaftar & pertanyaan seleksi divisinya
     * 6. anggota       — Hanya akses mobile, tidak bisa masuk panel admin
     */
    public function run(): void
    {
        // ── 1. Reset cache permission ──────────────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 2. Definisikan semua permission ────────────────────
        $permissions = [
            // User & Role Management
            'kelola_users',
            'kelola_roles',

            // Konten
            'kelola_berita',
            'kelola_galeri',
            'kelola_program',
            'kelola_pengurus',
            'kelola_materi',

            // Presensi & Agenda
            'kelola_agenda',
            'kelola_presensi',

            // E-Voting
            'kelola_voting',

            // E-Kas (Keuangan)
            'kelola_tagihan_kas',
            'kelola_transaksi_kas',
            'lihat_saldo_kas',

            // Rekrutmen
            'kelola_divisi',
            'kelola_pertanyaan_seleksi',
            'kelola_pendaftar',
            'kelola_pendaftar_divisi',           // Hanya pendaftar di divisinya

            // Chatbot RAG
            'kelola_rag_documents',

            // Akses umum
            'akses_panel_admin',
            'lihat_dashboard',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── 3. Buat role dan assign permission ─────────────────

        // ✅ SUPER ADMIN — Akses semua
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // ✅ KETUA UKM — Akses semua KECUALI user & role management
        $ketuaUkm = Role::firstOrCreate(['name' => 'ketua_ukm', 'guard_name' => 'web']);
        $ketuaUkm->syncPermissions([
            'akses_panel_admin',
            'lihat_dashboard',
            'kelola_berita',
            'kelola_galeri',
            'kelola_program',
            'kelola_pengurus',
            'kelola_materi',
            'kelola_agenda',
            'kelola_presensi',
            'kelola_voting',
            'kelola_tagihan_kas',
            'kelola_transaksi_kas',
            'lihat_saldo_kas',
            'kelola_divisi',
            'kelola_pertanyaan_seleksi',
            'kelola_pendaftar',
            'kelola_rag_documents',
        ]);

        // ✅ SEKRETARIS — Konten, agenda, presensi, materi
        $sekretaris = Role::firstOrCreate(['name' => 'sekretaris', 'guard_name' => 'web']);
        $sekretaris->syncPermissions([
            'akses_panel_admin',
            'lihat_dashboard',
            'kelola_berita',
            'kelola_galeri',
            'kelola_program',
            'kelola_pengurus',
            'kelola_materi',
            'kelola_agenda',
            'kelola_presensi',
        ]);

        // ✅ BENDAHARA — Khusus keuangan
        $bendahara = Role::firstOrCreate(['name' => 'bendahara', 'guard_name' => 'web']);
        $bendahara->syncPermissions([
            'akses_panel_admin',
            'lihat_dashboard',
            'kelola_tagihan_kas',
            'kelola_transaksi_kas',
            'lihat_saldo_kas',
        ]);

        // ✅ KETUA DIVISI — Khusus rekrutmen & materi divisinya
        $ketuaDivisi = Role::firstOrCreate(['name' => 'ketua_divisi', 'guard_name' => 'web']);
        $ketuaDivisi->syncPermissions([
            'akses_panel_admin',
            'lihat_dashboard',
            'kelola_pertanyaan_seleksi',
            'kelola_pendaftar_divisi',           // Hanya divisinya, bukan semua
            'kelola_materi',                     // Untuk upload materi divisinya
        ]);

        // ✅ ANGGOTA — Tidak bisa akses panel admin (hanya mobile app)
        $anggota = Role::firstOrCreate(['name' => 'anggota', 'guard_name' => 'web']);
        $anggota->syncPermissions([
            // Tidak punya akses_panel_admin — hanya untuk login mobile API
        ]);

        // ✅ DEMISIONER — Akun nonaktif (alumni / mantan pengurus)
        // Tidak punya permission apa pun. Akun tetap ada untuk arsip historis,
        // tapi tidak bisa login ke panel admin maupun mobile app.
        $demisioner = Role::firstOrCreate(['name' => 'demisioner', 'guard_name' => 'web']);
        $demisioner->syncPermissions([
            // Sengaja kosong — akun nonaktif total
        ]);

        $this->command->info('✅ 6 role berhasil dibuat dengan permission masing-masing.');
        $this->command->newLine();
        $this->command->info('   👑 super_admin   — Akses semua fitur');
        $this->command->info('   👨‍💼 ketua_ukm     — Akses semua kecuali user management');
        $this->command->info('   📝 sekretaris    — Konten, agenda, presensi, materi');
        $this->command->info('   💰 bendahara     — Keuangan (e-kas)');
        $this->command->info('   🏆 ketua_divisi  — Rekrutmen & materi divisinya');
        $this->command->info('   👥 anggota       — Hanya mobile app');
        $this->command->info('   🏛️  demisioner    — Akun nonaktif (alumni)');
    }
}
