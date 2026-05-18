<?php

namespace App\Filament\Resources\TransaksiKas\Pages;

use App\Filament\Resources\TransaksiKas\TransaksiKasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksiKas extends CreateRecord
{
    protected static string $resource = TransaksiKasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dicatat_oleh'] = auth()->id();
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Transaksi berhasil dicatat!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
