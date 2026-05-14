<?php

namespace App\Filament\Resources\RagDocuments\Tables;

use App\Filament\Resources\RagDocuments\RagDocumentResource;
use App\Jobs\ProcessPdfJob;
use App\Models\RagDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
                    ->color(fn(string $state, RagDocument $record) => $record->status === 'processing' ? 'warning' : 'info')
                    ->formatStateUsing(fn(string $state, RagDocument $record) => $record->status === 'processing'
                        ? "⚙️ {$state} chunks..."
                        : $state),

                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('lihat_progress')
                    ->label('Lihat Progress')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->visible(fn(RagDocument $r) => $r->status === 'processing')
                    ->modalHeading(fn(RagDocument $r) => "Progress: {$r->nama_file}")
                    ->modalContent(fn(RagDocument $r) => view('filament.modals.chunk-progress', ['document' => $r]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Action::make('lihat_isi')
                    ->label('Lihat Isi')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn(RagDocument $r) => $r->status === 'ready')
                    ->url(fn(RagDocument $r) => RagDocumentResource::getUrl('view', ['record' => $r])),

                Action::make('reprocess')
                    ->label('Proses Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(RagDocument $r) => $r->status === 'error')
                    ->requiresConfirmation()
                    ->action(function (RagDocument $record): void {
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
            ])->defaultSort('created_at', 'desc')
            ->poll('3s');

    }
}
