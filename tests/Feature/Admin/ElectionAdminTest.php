<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Elections\Pages\EditElection;
use App\Filament\Resources\Elections\Pages\ListElections;
use App\Filament\Resources\Elections\Pages\ViewElection;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use App\Services\ElectionService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ElectionAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    // ─── helpers ───────────────────────────────────────────────

    private function buatElection(array $attrs = []): Election
    {
        return Election::create(array_merge([
            'judul'           => 'Pemilihan Ketua UKM',
            'deskripsi'       => 'Test election',
            'posisi'          => 'Ketua UKM',
            'status'          => 'draft',
            'waktu_mulai'     => now()->subHour(),
            'waktu_selesai'   => now()->addHours(2),
            'is_anonim'       => true,
            'tampil_realtime' => false,
            'created_by'      => User::factory()->create()->id,
        ], $attrs));
    }

    private function buatKandidat(Election $election, User $user): Candidate
    {
        return Candidate::create([
            'election_id' => $election->id,
            'user_id'     => $user->id,
            'visi'        => 'Visi test',
            'misi'        => 'Misi test',
        ]);
    }

    private function buatSuara(Election $election, Candidate $candidate, User $voter): Vote
    {
        return Vote::create([
            'election_id'  => $election->id,
            'candidate_id' => $candidate->id,
            'voter_hash'   => hash('sha256', $voter->id . '-' . $election->id . '-ukm-mci-secret'),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // LIST PAGE — tabel & aksi baris
    // ══════════════════════════════════════════════════════════════

    public function test_list_page_dapat_diakses_semua_role_panel(): void
    {
        foreach (['super_admin', 'ketua_ukm', 'sekretaris', 'bendahara', 'ketua_divisi'] as $role) {
            $admin = $this->buatUser($role);
            Livewire::actingAs($admin)
                ->test(ListElections::class)
                ->assertSuccessful();
        }
    }

    public function test_aksi_edit_tersembunyi_untuk_election_selesai(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->assertTableActionHidden('edit', $election);
    }

    public function test_aksi_edit_tersembunyi_untuk_election_tie(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->assertTableActionHidden('edit', $election);
    }

    public function test_aksi_edit_tersedia_untuk_election_draft(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'draft']);

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->assertTableActionVisible('edit', $election);
    }

    public function test_aksi_aktifkan_tersedia_hanya_untuk_draft(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $draft    = $this->buatElection(['status' => 'draft']);
        $aktif    = $this->buatElection(['status' => 'aktif', 'judul' => 'Aktif']);

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->assertTableActionVisible('aktifkan', $draft)
            ->assertTableActionHidden('aktifkan', $aktif);
    }

    public function test_aksi_tutup_tersedia_hanya_untuk_aktif(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $aktif    = $this->buatElection(['status' => 'aktif']);
        $draft    = $this->buatElection(['status' => 'draft', 'judul' => 'Draft']);

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->assertTableActionVisible('tutup', $aktif)
            ->assertTableActionHidden('tutup', $draft);
    }

    public function test_eksekusi_aktifkan_mengubah_status_ke_aktif(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'draft']);

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->callTableAction('aktifkan', $election)
            ->assertHasNoTableActionErrors();

        $this->assertEquals('aktif', $election->fresh()->status);
    }

    public function test_eksekusi_tutup_mengubah_status_ke_selesai(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'aktif']);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));

        // Tidak seri: kandidat1 menang jelas
        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->callTableAction('tutup', $election)
            ->assertHasNoTableActionErrors();

        $this->assertEquals('selesai', $election->fresh()->status);
    }

    public function test_eksekusi_tutup_mengubah_status_ke_tie_jika_hasil_seri(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'aktif']);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));

        // Seri: masing-masing 2 suara
        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));

        Livewire::actingAs($admin)
            ->test(ListElections::class)
            ->callTableAction('tutup', $election)
            ->assertHasNoTableActionErrors();

        $this->assertEquals('tie', $election->fresh()->status);
    }

    // ══════════════════════════════════════════════════════════════
    // EDIT PAGE — proteksi election selesai/tie
    // ══════════════════════════════════════════════════════════════

    public function test_edit_page_bisa_diakses_untuk_election_draft(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'draft']);

        Livewire::actingAs($admin)
            ->test(EditElection::class, ['record' => $election->getKey()])
            ->assertSuccessful()
            ->assertNoRedirect();
    }

    public function test_edit_page_redirect_untuk_election_selesai(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);

        Livewire::actingAs($admin)
            ->test(EditElection::class, ['record' => $election->getKey()])
            ->assertRedirect();
    }

    public function test_edit_page_redirect_untuk_election_tie(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);

        Livewire::actingAs($admin)
            ->test(EditElection::class, ['record' => $election->getKey()])
            ->assertRedirect();
    }

    // ══════════════════════════════════════════════════════════════
    // VIEW PAGE — tampilan & aksi resolusi
    // ══════════════════════════════════════════════════════════════

    public function test_view_page_menampilkan_detail_election(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['judul' => 'Pemilihan Test View']);

        Livewire::actingAs($admin)
            ->test(ViewElection::class, ['record' => $election->getKey()])
            ->assertSuccessful()
            ->assertSee('Pemilihan Test View');
    }

    public function test_tombol_edit_tersembunyi_di_view_page_untuk_selesai(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'selesai', 'waktu_selesai' => now()->subHour()]);

        Livewire::actingAs($admin)
            ->test(ViewElection::class, ['record' => $election->getKey()])
            ->assertActionHidden('edit');
    }

    public function test_tombol_edit_tersedia_di_view_page_untuk_draft(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'draft']);

        Livewire::actingAs($admin)
            ->test(ViewElection::class, ['record' => $election->getKey()])
            ->assertActionVisible('edit');
    }

    // ── Resolusi Seri ───────────────────────────────────────────

    public function test_aksi_musyawarah_menetapkan_pemenang_dan_ubah_status_selesai(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));

        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));

        Livewire::actingAs($admin)
            ->test(ViewElection::class, ['record' => $election->getKey()])
            ->callAction('musyawarah', data: [
                'tie_winner_candidate_id' => $kandidat1->id,
                'tie_resolution_notes'    => 'Kandidat 1 dipilih melalui musyawarah.',
            ])
            ->assertHasNoActionErrors();

        $fresh = $election->fresh();
        $this->assertEquals('selesai',       $fresh->status);
        $this->assertEquals('deliberation',  $fresh->tie_resolution_type);
        $this->assertEquals($kandidat1->id,  $fresh->tie_winner_candidate_id);
        $this->assertEquals('Kandidat 1 dipilih melalui musyawarah.', $fresh->tie_resolution_notes);
        $this->assertNotNull($fresh->tie_resolved_at);
    }

    public function test_aksi_musyawarah_gagal_validasi_tanpa_catatan(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);
        $kandidat  = $this->buatKandidat($election, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat, $this->buatUser('anggota'));

        Livewire::actingAs($admin)
            ->test(ViewElection::class, ['record' => $election->getKey()])
            ->callAction('musyawarah', data: [
                'tie_winner_candidate_id' => $kandidat->id,
                // tie_resolution_notes tidak diisi
            ])
            ->assertHasActionErrors(['tie_resolution_notes']);
    }

    public function test_aksi_musyawarah_gagal_validasi_tanpa_kandidat_pemenang(): void
    {
        $admin    = $this->buatUser('ketua_ukm');
        $election = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);

        Livewire::actingAs($admin)
            ->test(ViewElection::class, ['record' => $election->getKey()])
            ->callAction('musyawarah', data: [
                'tie_resolution_notes' => 'Ada catatan tapi kandidat tidak dipilih',
            ])
            ->assertHasActionErrors(['tie_winner_candidate_id']);
    }

    // ══════════════════════════════════════════════════════════════
    // ELECTION SERVICE — createRevote
    // ══════════════════════════════════════════════════════════════

    public function test_create_revote_membuat_election_baru_dengan_kandidat_seri(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat3 = $this->buatKandidat($election, $this->buatUser('anggota'));

        // Kandidat 1 & 2 seri, kandidat 3 kalah
        $voter1 = $this->buatUser('anggota');
        $voter2 = $this->buatUser('anggota');
        $voter3 = $this->buatUser('anggota');

        $this->buatSuara($election, $kandidat1, $voter1);
        $this->buatSuara($election, $kandidat2, $voter2);
        // kandidat3 tidak dapat suara

        $this->actingAs($admin);

        $newElection = app(ElectionService::class)->createRevote($election);

        // Election baru dibuat
        $this->assertNotNull($newElection);
        $this->assertEquals('aktif', $newElection->status);
        $this->assertEquals($election->id, $newElection->parent_election_id);

        // Hanya 2 kandidat seri yang masuk revote (bukan kandidat3)
        $this->assertCount(2, $newElection->candidates);

        $userIds = $newElection->candidates->pluck('user_id');
        $this->assertContains($kandidat1->user_id, $userIds);
        $this->assertContains($kandidat2->user_id, $userIds);
        $this->assertNotContains($kandidat3->user_id, $userIds);
    }

    public function test_create_revote_menandai_parent_election_selesai_dengan_revote(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));

        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));

        $this->actingAs($admin);
        app(ElectionService::class)->createRevote($election);

        $parent = $election->fresh();
        $this->assertEquals('selesai',    $parent->status);
        $this->assertEquals('revote',     $parent->tie_resolution_type);
        $this->assertNotNull($parent->tie_resolved_at);
    }

    public function test_create_revote_menyalin_urut_kandidat(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));

        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));

        $this->actingAs($admin);
        $newElection = app(ElectionService::class)->createRevote($election);

        $newUruts  = $newElection->candidates->pluck('urut')->sort()->values();
        $origUruts = collect([$kandidat1->urut, $kandidat2->urut])->sort()->values();

        $this->assertEquals($origUruts, $newUruts);
    }

    public function test_create_revote_judul_mengandung_putaran_kedua(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection([
            'judul'         => 'Pemilihan Ketua',
            'status'        => 'tie',
            'waktu_selesai' => now()->subHour(),
        ]);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));

        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));

        $this->actingAs($admin);
        $newElection = app(ElectionService::class)->createRevote($election);

        $this->assertStringContainsString('Pemilihan Ketua', $newElection->judul);
        $this->assertStringContainsString('Putaran Kedua',   $newElection->judul);
    }

    public function test_create_revote_via_livewire_action(): void
    {
        $admin     = $this->buatUser('ketua_ukm');
        $election  = $this->buatElection(['status' => 'tie', 'waktu_selesai' => now()->subHour()]);
        $kandidat1 = $this->buatKandidat($election, $this->buatUser('anggota'));
        $kandidat2 = $this->buatKandidat($election, $this->buatUser('anggota'));

        $this->buatSuara($election, $kandidat1, $this->buatUser('anggota'));
        $this->buatSuara($election, $kandidat2, $this->buatUser('anggota'));

        Livewire::actingAs($admin)
            ->test(ViewElection::class, ['record' => $election->getKey()])
            ->callAction('mulai_revote')
            ->assertHasNoActionErrors();

        // Election baru telah dibuat
        $this->assertDatabaseCount('elections', 2);

        // Parent election bertanda revote
        $this->assertEquals('revote', $election->fresh()->tie_resolution_type);
    }
}
