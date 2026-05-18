<?php

namespace App\Filament\Resources\Pendaftars\Pages;

use App\Filament\Resources\Pendaftars\PendaftarResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePendaftar extends CreateRecord
{
    protected static string $resource = PendaftarResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pendaftar berhasil ditambahkan!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
