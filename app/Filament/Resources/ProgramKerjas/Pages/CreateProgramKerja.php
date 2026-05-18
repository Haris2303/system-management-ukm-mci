<?php

namespace App\Filament\Resources\ProgramKerjas\Pages;

use App\Filament\Resources\ProgramKerjas\ProgramKerjaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramKerja extends CreateRecord
{
    protected static string $resource = ProgramKerjaResource::class;

    /**
     * ⭐ Mutator: otomatis isi divisi_id dengan divisi user yang sedang login
     * (kecuali Super Admin & Ketua UKM yang bebas pilih divisi).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        // Super Admin & Ketua UKM bebas pilih divisi
        if (! $user?->hasAnyRole(['super_admin', 'ketua_ukm'])) {
            // Force divisi_id sesuai divisi user (anti-tampering)
            $data['divisi_id'] = $user?->divisi_id;
        }

        // Jika PIC kosong, default ke creator
        if (empty($data['pic_id'])) {
            $data['pic_id'] = auth()->id();
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Program kerja berhasil dibuat!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
