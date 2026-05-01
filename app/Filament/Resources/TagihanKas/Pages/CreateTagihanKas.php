<?php

namespace App\Filament\Resources\TagihanKas\Pages;

use App\Filament\Resources\TagihanKas\TagihanKasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTagihanKas extends CreateRecord
{
    protected static string $resource = TagihanKasResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tagihan berhasil dibuat!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
