<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'anggota',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'demisioner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        Storage::fake('public');
    }

    // ─── helper ────────────────────────────────────────────────

    private function demisionerUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('demisioner');
        return $user;
    }

    // ══════════════════════════════════════════════════════════════
    // GANTI PASSWORD — POST /api/profile/password
    // ══════════════════════════════════════════════════════════════

    public function test_ganti_password_berhasil_dengan_password_lama_yang_benar(): void
    {
        $user = $this->aktifUser(['password' => 'password_lama']);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [
                'current_password'          => 'password_lama',
                'new_password'              => 'password_baru123',
                'new_password_confirmation' => 'password_baru123',
            ])
            ->assertOk()
            ->assertJsonPath('pesan', 'Password berhasil diperbarui.');
    }

    public function test_bisa_login_dengan_password_baru_setelah_ganti(): void
    {
        $user = $this->aktifUser(['password' => 'password_lama']);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [
                'current_password'          => 'password_lama',
                'new_password'              => 'password_baru123',
                'new_password_confirmation' => 'password_baru123',
            ])->assertOk();

        // Login dengan password baru harus berhasil
        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password_baru123',
        ])->assertOk();
    }

    public function test_tidak_bisa_login_dengan_password_lama_setelah_ganti(): void
    {
        $user = $this->aktifUser(['password' => 'password_lama']);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [
                'current_password'          => 'password_lama',
                'new_password'              => 'password_baru123',
                'new_password_confirmation' => 'password_baru123',
            ])->assertOk();

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password_lama',
        ])->assertUnprocessable();
    }

    public function test_ganti_password_gagal_jika_password_lama_salah(): void
    {
        $user = $this->aktifUser(['password' => 'password_benar']);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [
                'current_password'          => 'password_salah',
                'new_password'              => 'password_baru123',
                'new_password_confirmation' => 'password_baru123',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.current_password.0', 'Password saat ini tidak sesuai.');
    }

    public function test_ganti_password_gagal_jika_password_baru_kurang_8_karakter(): void
    {
        $user = $this->aktifUser(['password' => 'password_lama']);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [
                'current_password'          => 'password_lama',
                'new_password'              => 'pendek',
                'new_password_confirmation' => 'pendek',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_ganti_password_gagal_jika_konfirmasi_tidak_cocok(): void
    {
        $user = $this->aktifUser(['password' => 'password_lama']);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [
                'current_password'          => 'password_lama',
                'new_password'              => 'password_baru123',
                'new_password_confirmation' => 'berbeda_sekali',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_ganti_password_gagal_jika_semua_field_kosong(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password', 'new_password']);
    }

    // ── Hak Akses: Ganti Password ───────────────────────────────

    public function test_ganti_password_memerlukan_autentikasi(): void
    {
        $this->postJson('/api/profile/password', [
            'current_password'          => 'lama',
            'new_password'              => 'baru12345',
            'new_password_confirmation' => 'baru12345',
        ])->assertUnauthorized();
    }

    public function test_ganti_password_diblokir_untuk_demisioner(): void
    {
        $user = $this->demisionerUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/password', [
                'current_password'          => 'password123',
                'new_password'              => 'baru12345',
                'new_password_confirmation' => 'baru12345',
            ])
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // UPDATE AVATAR — POST /api/profile/avatar
    // ══════════════════════════════════════════════════════════════

    // ── Mode: emoji ─────────────────────────────────────────────

    public function test_update_avatar_emoji_berhasil(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode'  => 'emoji',
                'emoji' => '😎',
                'bg'    => 'ff5733',
            ])
            ->assertOk()
            ->assertJsonPath('pesan', 'Avatar emoji berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id'     => $user->id,
            'avatar' => 'emoji:😎:ff5733',
        ]);
    }

    public function test_update_avatar_emoji_gagal_tanpa_field_bg(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode'  => 'emoji',
                'emoji' => '😎',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['bg']);
    }

    public function test_update_avatar_emoji_gagal_jika_bg_bukan_hex_valid(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode'  => 'emoji',
                'emoji' => '😎',
                'bg'    => 'bukan-hex',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['bg']);
    }

    public function test_update_avatar_emoji_gagal_tanpa_field_emoji(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode' => 'emoji',
                'bg'   => 'ff5733',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['emoji']);
    }

    // ── Mode: photo ─────────────────────────────────────────────

    public function test_update_avatar_foto_berhasil_disimpan(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode' => 'photo',
                'foto' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
            ])
            ->assertOk()
            ->assertJsonPath('pesan', 'Foto profil berhasil diperbarui.');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertNotNull($user->photo_uploaded_at);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_update_avatar_foto_gagal_jika_cooldown_aktif(): void
    {
        // Upload baru 3 hari lalu → cooldown 2 minggu belum habis
        $user = $this->aktifUser([
            'photo_uploaded_at' => now()->subDays(3),
        ]);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode' => 'photo',
                'foto' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->assertUnprocessable()
            ->assertJsonPath('kode', 'COOLDOWN_AKTIF');
    }

    public function test_update_avatar_foto_berhasil_setelah_cooldown_selesai(): void
    {
        // Upload terakhir 3 minggu lalu → cooldown sudah lewat
        $user = $this->aktifUser([
            'photo_uploaded_at' => now()->subWeeks(3),
        ]);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode' => 'photo',
                'foto' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->assertOk();
    }

    public function test_update_avatar_foto_gagal_jika_file_bukan_gambar(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', [
                'mode' => 'photo',
                'foto' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['foto']);
    }

    public function test_update_avatar_foto_gagal_jika_tidak_ada_file(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', ['mode' => 'photo'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['foto']);
    }

    // ── Mode: last_photo ────────────────────────────────────────

    public function test_update_avatar_last_photo_berhasil_jika_ada_foto_sebelumnya(): void
    {
        $user = $this->aktifUser([
            'last_photo_path'   => 'avatars/foto_lama.jpg',
            'photo_uploaded_at' => now()->subWeeks(3),
        ]);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', ['mode' => 'last_photo'])
            ->assertOk()
            ->assertJsonPath('pesan', 'Avatar berhasil dikembalikan ke foto sebelumnya.');

        $this->assertDatabaseHas('users', [
            'id'     => $user->id,
            'avatar' => 'avatars/foto_lama.jpg',
        ]);
    }

    public function test_update_avatar_last_photo_gagal_jika_belum_pernah_upload(): void
    {
        $user = $this->aktifUser(['last_photo_path' => null]);

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', ['mode' => 'last_photo'])
            ->assertUnprocessable()
            ->assertJsonPath('kode', 'TIDAK_ADA_FOTO_LAMA');
    }

    // ── Mode: tidak valid ───────────────────────────────────────

    public function test_update_avatar_gagal_dengan_mode_tidak_dikenal(): void
    {
        $user = $this->aktifUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', ['mode' => 'mode_aneh'])
            ->assertUnprocessable()
            ->assertJsonPath('pesan', 'Mode tidak valid.');
    }

    // ── Hak Akses: Update Avatar ────────────────────────────────

    public function test_update_avatar_memerlukan_autentikasi(): void
    {
        $this->postJson('/api/profile/avatar', ['mode' => 'emoji', 'emoji' => '🔥', 'bg' => '000000'])
            ->assertUnauthorized();
    }

    public function test_update_avatar_diblokir_untuk_demisioner(): void
    {
        $user = $this->demisionerUser();

        $this->withToken($this->tokenFor($user))
            ->postJson('/api/profile/avatar', ['mode' => 'emoji', 'emoji' => '🔥', 'bg' => '000000'])
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    // ══════════════════════════════════════════════════════════════
    // ID CARD — GET /api/id-card/me & /api/id-card/{userId}
    // ══════════════════════════════════════════════════════════════

    public function test_id_card_me_mengembalikan_data_kartu_pengguna_yang_login(): void
    {
        $user = $this->aktifUser();

        $expectedMemberId = 'MCI-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        $this->withToken($this->tokenFor($user))
            ->getJson('/api/id-card/me')
            ->assertOk()
            ->assertJsonPath('member_id', $expectedMemberId)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.name', $user->name)
            ->assertJsonStructure(['user', 'member_id', 'foto_url', 'card_url']);
    }

    public function test_id_card_me_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/id-card/me')->assertUnauthorized();
    }

    public function test_id_card_publik_bisa_diakses_tanpa_autentikasi(): void
    {
        $user = User::factory()->create();

        $this->getJson("/api/id-card/{$user->id}")->assertOk();
    }

    public function test_id_card_publik_mengembalikan_data_pengguna_lain(): void
    {
        $user = User::factory()->create();

        $this->getJson("/api/id-card/{$user->id}")
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.name', $user->name);
    }

    public function test_id_card_publik_tidak_mengekspos_email_dan_nomor_hp(): void
    {
        $user = User::factory()->create([
            'email' => 'rahasia@example.com',
            'no_hp' => '08123456789',
        ]);

        $response = $this->getJson("/api/id-card/{$user->id}")->assertOk();

        // Email dan no_hp tidak boleh ada di response publik
        $this->assertArrayNotHasKey('email', $response->json('user'));
        $this->assertArrayNotHasKey('no_hp', $response->json('user'));
        $this->assertNotContains('rahasia@example.com', $response->json());
        $this->assertNotContains('08123456789', $response->json());
    }

    public function test_id_card_publik_mengembalikan_404_untuk_user_tidak_ada(): void
    {
        $this->getJson('/api/id-card/99999')->assertNotFound();
    }

    public function test_format_member_id_menggunakan_4_digit_dengan_leading_zero(): void
    {
        $user = $this->aktifUser();

        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/id-card/me')
            ->assertOk();

        $memberId = $response->json('member_id');
        $this->assertMatchesRegularExpression('/^MCI-\d{4}$/', $memberId);
    }

    // ══════════════════════════════════════════════════════════════
    // RINGKASAN HAK AKSES — semua endpoint protected
    // ══════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\DataProvider('protectedEndpointsProvider')]
    public function test_endpoint_protected_menolak_request_tanpa_token(
        string $method,
        string $url
    ): void {
        $this->json($method, $url)->assertUnauthorized();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('demisionerBlockedEndpointsProvider')]
    public function test_endpoint_diblokir_untuk_akun_demisioner(
        string $method,
        string $url
    ): void {
        $user = $this->demisionerUser();

        $this->withToken($this->tokenFor($user))
            ->json($method, $url)
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    public static function protectedEndpointsProvider(): array
    {
        return [
            'lihat profil'    => ['GET',  '/api/profile'],
            'ganti password'  => ['POST', '/api/profile/password'],
            'update avatar'   => ['POST', '/api/profile/avatar'],
            'id card sendiri' => ['GET',  '/api/id-card/me'],
        ];
    }

    public static function demisionerBlockedEndpointsProvider(): array
    {
        return [
            'lihat profil'   => ['GET',  '/api/profile'],
            'ganti password' => ['POST', '/api/profile/password'],
            'update avatar'  => ['POST', '/api/profile/avatar'],
        ];
    }
}
