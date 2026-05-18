<?php

namespace App\Filament\Resources\TagihanKas\Pages;

use App\Filament\Resources\TagihanKas\TagihanKasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTagihanKas extends EditRecord
{
    protected static string $resource = TagihanKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Tagihan berhasil diperbarui!';
    }
}
