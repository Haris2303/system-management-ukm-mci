<?php

namespace App\Services;

use App\Models\Pendaftar;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PendaftarService
{
    public const DEFAULT_PASSWORD = 'password123';

    public function luluskan(Pendaftar $pendaftar): User
    {
        return DB::transaction(function () use ($pendaftar): User {
            $email = $pendaftar->effectiveEmail();

            // pastikan tidak ada user lain yang sudah pakai email ini
            $existing = User::where('email', $email)->first();
            if ($existing && $existing->id !== optional($pendaftar->user)->id) {
                throw new \RuntimeException("Email {$email} sudah digunakan akun lain.");
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $pendaftar->nama,
                    'email'     => $email,
                    'password'  => Hash::make(self::DEFAULT_PASSWORD),
                    'divisi_id' => $pendaftar->divisi_id,
                    'no_hp'     => $pendaftar->no_hp,
                ]
            );

            if (! $user->hasRole('anggota')) {
                $user->assignRole('anggota');
            }

            $pendaftar->update([
                'status'  => 'lulus',
                'user_id' => $user->id,
            ]);

            return $user;
        });
    }

    public function tolak(Pendaftar $pendaftar): void
    {
        $pendaftar->update(['status' => 'ditolak']);
    }
}
