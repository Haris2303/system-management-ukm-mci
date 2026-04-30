<?php

namespace App\Filament\Resources\RagDocuments\Tables;

use App\Jobs\ProcessPdfJob;
use App\Models\RagDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RagDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_file')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->placeholder('–'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'ready'      => 'success',
                        'processing' => 'warning',
                        'error'      => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'ready'      => '✅ Siap',
                        'processing' => '⏳ Diproses',
                        'error'      => '❌ Error',
                        default      => $state,
                    }),

                TextColumn::make('total_chunks')
                    ->label('Chunks')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('reprocess')
                    ->label('Proses Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(RagDocument $r) => in_array($r->status, ['error', 'ready']))
                    ->requiresConfirmation()
                    ->action(function (RagDocument $record): void {
                        // Hapus chunks lama
                        $record->chunks()->delete();
                        $record->update(['status' => 'processing', 'total_chunks' => 0]);
                        ProcessPdfJob::dispatch($record->id);
                        Notification::make()->title('Dokumen sedang diproses ulang!')->warning()->send();
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }
}
