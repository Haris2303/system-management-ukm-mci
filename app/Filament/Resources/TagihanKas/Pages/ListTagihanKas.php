<?php

namespace App\Filament\Resources\TagihanKas\Pages;

use App\Filament\Resources\TagihanKas\TagihanKasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTagihanKas extends ListRecords
{
    protected static string $resource = TagihanKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Buat Tagihan Baru'),
        ];
    }
}
