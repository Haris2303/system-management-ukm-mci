<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private const STORAGE_DISK = 'public';
    private const AVATAR_DIR   = 'avatars';
    private const MAX_SIZE_KB  = 2048;

    // GET /api/profile
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('divisi');

        return response()->json([
            'pesan' => 'Data profil berhasil dimuat.',
            'data'  => $this->buildProfileData($user),
        ]);
    }

    // POST /api/profile/avatar
    public function updateAvatar(Request $request): JsonResponse
    {
        $mode = $request->input('mode');

        return match ($mode) {
            'photo'      => $this->handlePhotoUpload($request),
            'emoji'      => $this->handleEmojiAvatar($request),
            'last_photo' => $this->handleLastPhoto($request),
            default      => response()->json(['pesan' => 'Mode tidak valid.'], 422),
        };
    }

    // POST /api/profile/password
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password'          => ['required', 'string'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'pesan'  => 'Password saat ini tidak sesuai.',
                'errors' => ['current_password' => ['Password saat ini tidak sesuai.']],
            ], 422);
        }

        $user->update(['password' => $request->new_password]);

        return response()->json(['pesan' => 'Password berhasil diperbarui.']);
    }

    private function handlePhotoUpload(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->canUploadPhoto()) {
            $endsAt = $user->photoUploadCooldownEndsAt();

            return response()->json([
                'pesan' => 'Foto baru dapat diunggah mulai ' . $endsAt->translatedFormat('d F Y') . '.',
                'kode'  => 'COOLDOWN_AKTIF',
                'data'  => ['cooldown_selesai' => $endsAt->toIso8601String()],
            ], 422);
        }

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:' . self::MAX_SIZE_KB],
        ]);

        $path = $request->file('foto')->store(self::AVATAR_DIR, self::STORAGE_DISK);

        $user->update([
            'avatar'            => $path,
            'last_photo_path'   => $path,
            'photo_uploaded_at' => now(),
        ]);

        return response()->json([
            'pesan' => 'Foto profil berhasil diperbarui.',
            'data'  => $this->buildProfileData($user->fresh('divisi')),
        ]);
    }

    private function handleEmojiAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'emoji' => ['required', 'string', 'max:10'],
            'bg'    => ['required', 'string', 'regex:/^[0-9a-fA-F]{6}$/'],
        ]);

        $user = $request->user();
        $user->update(['avatar' => "emoji:{$request->emoji}:{$request->bg}"]);

        return response()->json([
            'pesan' => 'Avatar emoji berhasil diperbarui.',
            'data'  => $this->buildProfileData($user->fresh('divisi')),
        ]);
    }

    private function handleLastPhoto(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->last_photo_path) {
            return response()->json([
                'pesan' => 'Belum ada foto yang pernah diunggah.',
                'kode'  => 'TIDAK_ADA_FOTO_LAMA',
            ], 422);
        }

        $user->update(['avatar' => $user->last_photo_path]);

        return response()->json([
            'pesan' => 'Avatar berhasil dikembalikan ke foto sebelumnya.',
            'data'  => $this->buildProfileData($user->fresh('divisi')),
        ]);
    }

    private function buildProfileData($user): array
    {
        return [
            'id'               => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'no_hp'            => $user->no_hp,
            'divisi'           => $user->divisi?->nama,
            'role_label'       => $user->role_label,
            'avatar'           => $user->avatar,
            'avatar_url'       => $this->resolveAvatarUrl($user->avatar),
            'last_photo_url'   => $user->last_photo_path
                ? Storage::disk(self::STORAGE_DISK)->url($user->last_photo_path)
                : null,
            'can_upload_photo' => $user->canUploadPhoto(),
            'cooldown_selesai' => $user->photoUploadCooldownEndsAt()?->toIso8601String(),
        ];
    }

    private function resolveAvatarUrl(?string $avatar): ?string
    {
        if (! $avatar) return null;
        if (str_starts_with($avatar, 'emoji:')) return null;
        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) return $avatar;

        return Storage::disk(self::STORAGE_DISK)->url($avatar);
    }
}
