<?php

namespace App\Filament\Resources\Divisis\Pages;

use App\Filament\Resources\Divisis\DivisiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDivisi extends CreateRecord
{
    protected static string $resource = DivisiResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Divisi berhasil dibuat!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
