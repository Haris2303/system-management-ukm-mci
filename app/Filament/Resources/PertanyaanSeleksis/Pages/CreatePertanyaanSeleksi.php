<?php

namespace App\Filament\Resources\PertanyaanSeleksis\Pages;

use App\Filament\Resources\PertanyaanSeleksis\PertanyaanSeleksiResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePertanyaanSeleksi extends CreateRecord
{
    protected static string $resource = PertanyaanSeleksiResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pertanyaan berhasil ditambahkan!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
