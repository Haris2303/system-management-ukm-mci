<?php

namespace App\Filament\Resources\PertanyaanSeleksis\Pages;

use App\Filament\Resources\PertanyaanSeleksis\PertanyaanSeleksiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPertanyaanSeleksis extends ListRecords
{
    protected static string $resource = PertanyaanSeleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Pertanyaan'),
        ];
    }
}
