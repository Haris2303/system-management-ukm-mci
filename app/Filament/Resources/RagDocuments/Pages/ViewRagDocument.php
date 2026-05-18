<?php

namespace App\Filament\Resources\RagDocuments\Pages;

use App\Filament\Resources\RagDocuments\RagDocumentResource;
use App\Models\RagChunk;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ViewRagDocument extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = RagDocumentResource::class;

    public function getView(): string
    {
        return 'filament.resources.rag-documents.pages.view-rag-document';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => RagChunk::query()->where('document_id', $this->record->id))
            ->columns([
                TextColumn::make('chunk_index')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('content')
                    ->label('Isi Chunk')
                    ->wrap()
                    ->limit(300),

                TextColumn::make('token_count')
                    ->label('Token')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->width('80px'),
            ])
            ->defaultSort('chunk_index')
            ->paginated([10, 25, 50])
            ->striped();
    }
}
