<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset permission cache agar tidak bocor antar test
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'anggota',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'demisioner', 'guard_name' => 'web']);
    }

    // ══════════════════════════════════════════════════════════════
    // LOGIN
    // POST /api/auth/login
    // ══════════════════════════════════════════════════════════════

    public function test_login_berhasil_mengembalikan_token(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'rahasia123',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonStructure([
                'pesan',
                'data' => ['token', 'user' => ['id', 'name', 'email']],
            ]);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $user = User::factory()->create(['password' => 'benar123']);

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'salah123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_gagal_dengan_email_tidak_terdaftar(): void
    {
        $this->postJson('/api/auth/login', [
            'email'    => 'tidakada@example.com',
            'password' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_gagal_jika_email_tidak_diisi(): void
    {
        $this->postJson('/api/auth/login', ['password' => 'password123'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_gagal_jika_password_tidak_diisi(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', ['email' => $user->email])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_gagal_jika_format_email_tidak_valid(): void
    {
        $this->postJson('/api/auth/login', [
            'email'    => 'bukan-email',
            'password' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_diblokir_untuk_akun_demisioner(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $user->assignRole('demisioner');

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ])
            ->assertForbidden()
            ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    public function test_login_menghapus_token_lama(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $user->createToken('token-lama-1');
        $user->createToken('token-lama-2');

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertOk();

        // Token lama dihapus, hanya ada 1 token baru
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    // ══════════════════════════════════════════════════════════════
    // REGISTER
    // POST /api/auth/register
    // ══════════════════════════════════════════════════════════════

    public function test_register_berhasil_membuat_akun_baru(): void
    {
        $this->postJson('/api/auth/register', [
            'name'                  => 'Budi Santoso',
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'budi@example.com')
            ->assertJsonStructure([
                'pesan',
                'data' => ['token', 'user' => ['id', 'name', 'email']],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
    }

    public function test_register_mengembalikan_token_yang_valid(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Sari Dewi',
            'email'                 => 'sari@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $token = $response->json('data.token');

        // Token yang diterima harus bisa dipakai untuk mengakses endpoint protected
        $this->withToken($token)
             ->getJson('/api/profile')
             ->assertOk();
    }

    public function test_register_gagal_dengan_email_yang_sudah_terdaftar(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->postJson('/api/auth/register', [
            'name'                  => 'User Baru',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_gagal_dengan_password_kurang_dari_8_karakter(): void
    {
        $this->postJson('/api/auth/register', [
            'name'                  => 'Budi',
            'email'                 => 'budi@example.com',
            'password'              => 'pendek',
            'password_confirmation' => 'pendek',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_gagal_ketika_konfirmasi_password_tidak_cocok(): void
    {
        $this->postJson('/api/auth/register', [
            'name'                  => 'Budi',
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'berbeda456',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_gagal_jika_nama_tidak_diisi(): void
    {
        $this->postJson('/api/auth/register', [
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_register_gagal_jika_semua_field_kosong(): void
    {
        $this->postJson('/api/auth/register', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    // ══════════════════════════════════════════════════════════════
    // LOGOUT
    // POST /api/auth/logout
    // ══════════════════════════════════════════════════════════════

    public function test_logout_berhasil_menghapus_token_aktif(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)
             ->postJson('/api/auth/logout')
             ->assertOk()
             ->assertJsonPath('pesan', 'Anda berhasil keluar. Sampai jumpa!');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_memerlukan_autentikasi(): void
    {
        $this->postJson('/api/auth/logout')
             ->assertUnauthorized();
    }

    public function test_token_tidak_dapat_digunakan_setelah_logout(): void
    {
        $user      = User::factory()->create();
        $tokenObj  = $user->createToken('mobile');
        $plainText = $tokenObj->plainTextToken;
        $tokenId   = $tokenObj->accessToken->id;

        // Logout
        $this->withToken($plainText)->postJson('/api/auth/logout')->assertOk();

        // Token harus sudah terhapus dari database
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);

        // Reset guard agar request berikutnya benar-benar re-autentikasi
        $this->app['auth']->forgetGuards();

        // Coba pakai token yang sama — harus ditolak
        $this->withToken($plainText)
             ->getJson('/api/profile')
             ->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // PROFIL (GET /api/profile) — juga menguji auth guard secara umum
    // ══════════════════════════════════════════════════════════════

    public function test_profil_mengembalikan_data_pengguna_yang_login(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
             ->getJson('/api/profile')
             ->assertOk()
             ->assertJsonPath('data.email', $user->email)
             ->assertJsonStructure([
                 'data' => ['id', 'name', 'email'],
             ]);
    }

    public function test_profil_memerlukan_autentikasi(): void
    {
        $this->getJson('/api/profile')->assertUnauthorized();
    }

    public function test_profil_diblokir_untuk_akun_demisioner(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('demisioner');

        $this->actingAs($user, 'sanctum')
             ->getJson('/api/profile')
             ->assertForbidden()
             ->assertJsonPath('kode', 'AKUN_DEMISIONER');
    }

    public function test_profil_berisi_data_divisi_jika_user_memiliki_divisi(): void
    {
        $divisi = \App\Models\Divisi::create([
            'nama'      => 'Web Development',
            'slug'      => 'web-development',
            'icon'      => '💻',
            'is_active' => true,
            'urut'      => 1,
        ]);

        /** @var User $user */
        $user  = User::factory()->create(['divisi_id' => $divisi->id]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
             ->getJson('/api/profile')
             ->assertOk()
             ->assertJsonPath('data.divisi', $divisi->nama);
    }
}
