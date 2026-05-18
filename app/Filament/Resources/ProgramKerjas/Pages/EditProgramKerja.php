<?php

namespace App\Filament\Resources\ProgramKerjas\Pages;

use App\Filament\Resources\ProgramKerjas\ProgramKerjaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProgramKerja extends EditRecord
{
    protected static string $resource = ProgramKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Cegah user non-admin mengubah divisi_id (anti-tampering).
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();
        if (! $user?->hasAnyRole(['super_admin', 'ketua_ukm'])) {
            // Restore divisi_id ke nilai asli — tidak boleh ubah
            $data['divisi_id'] = $this->getRecord()->divisi_id;
        }
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Program kerja berhasil diperbarui!';
    }
}
