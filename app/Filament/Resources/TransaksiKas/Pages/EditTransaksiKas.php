<?php

namespace App\Filament\Resources\TransaksiKas\Pages;

use App\Filament\Resources\TransaksiKas\TransaksiKasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiKas extends EditRecord
{
    protected static string $resource = TransaksiKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Transaksi berhasil diperbarui!';
    }
}
