<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login dan dapatkan API token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi yang Anda masukkan salah.'],
            ]);
        }

        // Hapus token lama, buat yang baru
        $user->tokens()->delete();
        $token = $user->createToken('ukm-mci-mobile')->plainTextToken;

        return response()->json([
            'pesan' => 'Login berhasil. Selamat datang, ' . $user->name . '!',
            'data'  => [
                'user'  => $user->only(['id', 'name', 'email']),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Registrasi anggota baru.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'          => 'Nama wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.unique'           => 'Email ini sudah terdaftar.',
            'password.required'      => 'Kata sandi wajib diisi.',
            'password.min'           => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'     => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user  = User::create($validated);
        $token = $user->createToken('ukm-mci-mobile')->plainTextToken;

        return response()->json([
            'pesan' => 'Registrasi berhasil! Selamat bergabung di UKM MCI.',
            'data'  => [
                'user'  => $user->only(['id', 'name', 'email']),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Logout dan hapus token aktif.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'pesan' => 'Anda berhasil keluar. Sampai jumpa!',
        ]);
    }
}
