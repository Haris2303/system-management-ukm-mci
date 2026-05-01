<?php

namespace App\Filament\Resources\PertanyaanSeleksis\Pages;

use App\Filament\Resources\PertanyaanSeleksis\PertanyaanSeleksiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPertanyaanSeleksi extends EditRecord
{
    protected static string $resource = PertanyaanSeleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pertanyaan berhasil diperbarui!';
    }
}
