<?php

namespace App\Filament\Resources\RagDocuments\Pages;

use App\Filament\Resources\RagDocuments\RagDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRagDocuments extends ListRecords
{
    protected static string $resource = RagDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Upload Dokumen PDF'),
        ];
    }
}
