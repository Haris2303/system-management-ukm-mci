<?php

namespace App\Filament\Resources\Divisis\Pages;

use App\Filament\Resources\Divisis\DivisiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDivisi extends EditRecord
{
    protected static string $resource = DivisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Divisi berhasil diperbarui!';
    }
}
