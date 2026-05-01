<?php

namespace App\Filament\Resources\Pendaftars\Pages;

use App\Filament\Resources\Pendaftars\PendaftarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPendaftars extends ListRecords
{
    protected static string $resource = PendaftarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Pendaftar Manual'),
        ];
    }
}
