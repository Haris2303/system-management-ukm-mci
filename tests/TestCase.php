<?php

namespace Tests;

use App\Models\Agenda;
use App\Models\Divisi;
use App\Models\Pendaftar;
use App\Models\Post;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    public function aktingAs(User $user): static
    {
        $this->actingAs($user);
        return $this;
    }

    public function buatUser(string $role, array $attrs = []): User
    {
        /** @var User $user */
        $user = User::factory()->create($attrs);
        $user->assignRole($role);
        return $user;
    }

    public function buatDivisi(string $nama = 'Web Dev'): Divisi
    {
        static $urut = 1;
        return Divisi::create([
            'nama'      => $nama,
            'slug'      => Str::slug($nama . '-' . $urut++),
            'icon'      => '💻',
            'is_active' => true,
            'urut'      => $urut,
        ]);
    }

    public function buatPost(array $attrs = []): Post
    {
        return Post::create(array_merge([
            'judul'     => 'Berita Test',
            'konten'    => 'Isi berita yang panjang untuk testing...',
            'ringkasan' => 'Ringkasan berita',
            'kategori'  => 'Berita',
            'status'    => 'draft',
            'author_id' => User::factory()->create()->id,
        ], $attrs));
    }

    public function buatProker(array $attrs = []): ProgramKerja
    {
        return ProgramKerja::create(array_merge([
            'divisi_id'       => null,
            'nama_proker'     => 'Proker Test',
            'deskripsi'       => 'Deskripsi',
            'tanggal_mulai'   => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->addDays(20)->toDateString(),
            'status'          => 'active',
            'progress_persen' => 0,
        ], $attrs));
    }

    public function buatAgenda(array $attrs = []): Agenda
    {
        return Agenda::create(array_merge([
            'nama_agenda'   => 'Rapat Rutin',
            'deskripsi'     => 'Deskripsi agenda',
            'waktu_mulai'   => now()->subHour(),
            'waktu_selesai' => now()->addHours(2),
            'lokasi'        => 'Ruang A',
            'is_active'     => true,
            'qr_code_token' => Str::random(32),
        ], $attrs));
    }

    public function buatPendaftar(Divisi $divisi, array $attrs = []): Pendaftar
    {
        return Pendaftar::create(array_merge([
            'divisi_id' => $divisi->id,
            'nama'      => 'Budi Santoso',
            'nim'       => '2023' . rand(1000, 9999),
            'email'     => 'pendaftar@example.com',
            'no_hp'     => '081234567890',
            'angkatan'  => '2023',
            'status'    => 'menunggu',
        ], $attrs));
    }
}
