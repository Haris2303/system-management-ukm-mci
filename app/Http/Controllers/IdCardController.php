<?php

namespace App\Http\Controllers;

use App\Models\IdCardSetting;
use App\Models\User;
use App\Support\IdCardTemplates;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IdCardController extends Controller
{
    /** ID Card printable milik user yang sedang login */
    public function preview(Request $request)
    {
        return $this->show($request, $request->user()->id);
    }

    /** ID Card printable berdasarkan user id */
    public function show(Request $request, string $userId)
    {
        $user = User::with('divisi', 'roles')->findOrFail($userId);

        $base           = $request->getSchemeAndHttpHost();
        $template       = IdCardTemplates::find(IdCardSetting::activeTemplate());
        $backgroundImage = IdCardSetting::backgroundImageUrl();

        $profileUrl = $base . route('anggota.show', $user->public_id, false);

        $qrCode = QrCode::format('svg')
            ->size(120)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($profileUrl);

        $memberId = 'MCI-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        $fotoUrl = $user->avatar_url;

        return view('id-card.show', compact(
            'user', 'template', 'backgroundImage', 'qrCode', 'memberId', 'fotoUrl', 'profileUrl'
        ));
    }

    /** Profil publik — ditampilkan saat QR code di-scan */
    public function publicProfile(string $publicId)
    {
        $user = User::with('divisi', 'roles')->where('public_id', $publicId)->firstOrFail();

        $memberId = 'MCI-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        $fotoUrl = $user->avatar_url;

        return view('landing.anggota.show', compact('user', 'memberId', 'fotoUrl'));
    }

    /** API: data ID Card milik user yang sedang login (Sanctum) */
    public function apiMe(Request $request): JsonResponse
    {
        return $this->apiShow($request->user()->id);
    }

    /** API: data ID Card berdasarkan userId (publik) */
    public function apiShowUser(string $userId): JsonResponse
    {
        return $this->apiShow($userId);
    }

    private function apiShow(string $userId): JsonResponse
    {
        $user  = User::with('divisi', 'roles')->findOrFail($userId);
        $base  = request()->getSchemeAndHttpHost();

        $memberId        = 'MCI-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $fotoUrl         = $user->avatar_url;
        $backgroundImage = IdCardSetting::backgroundImageUrl();
        $cardUrl         = $base . route('id-card.show', $user->id, false);
        $profileUrl      = $base . route('anggota.show', $user->public_id, false);

        return response()->json([
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'no_hp'      => $user->no_hp,
                'divisi'     => $user->divisi?->nama,
                'role'       => $user->roles->first()?->name,
                'role_label' => $user->role_label ?? ($user->roles->first()?->name ?? 'Anggota'),
            ],
            'member_id'            => $memberId,
            'foto_url'             => $fotoUrl,
            'background_image_url' => $backgroundImage,
            'card_url'             => $cardUrl,
            'profile_url'          => $profileUrl,
        ]);
    }
}
