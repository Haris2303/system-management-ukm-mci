<?php

namespace App\Services;

use App\Mail\PendaftarDitolak;
use App\Mail\PendaftarLulus;
use App\Models\Pendaftar;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PendaftarService
{
    public const DEFAULT_PASSWORD = 'password123';

    public function luluskan(Pendaftar $pendaftar): User
    {
        // Wajib ada email dari formulir pendaftaran
        if (empty($pendaftar->email)) {
            throw new \RuntimeException(
                "Pendaftar {$pendaftar->nama} tidak memiliki email. "
                . 'Lengkapi email di formulir sebelum meluluskan.'
            );
        }

        $email = $pendaftar->email;

        $user = DB::transaction(function () use ($pendaftar, $email): User {
            // Pastikan tidak ada user lain yang sudah pakai email ini
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

        // Kirim notifikasi ke email yang pendaftar inputkan di formulir
        Mail::to($email)->send(new PendaftarLulus($pendaftar));

        return $user;
    }

    public function tolak(Pendaftar $pendaftar): void
    {
        $pendaftar->update(['status' => 'ditolak']);

        // Kirim notifikasi email hanya jika pendaftar memiliki email asli
        if ($pendaftar->email) {
            Mail::to($pendaftar->email)->send(new PendaftarDitolak($pendaftar));
        }
    }
}
