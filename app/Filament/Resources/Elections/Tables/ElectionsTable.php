<?php

namespace App\Filament\Resources\Elections\Tables;

use App\Models\Election;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ElectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul Pemilihan')->searchable()->sortable()->weight('bold'),

                TextColumn::make('posisi')
                    ->label('Posisi')->badge()->color('primary'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'aktif'   => 'success',
                        'draft'   => 'gray',
                        'selesai' => 'info',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'aktif'   => '🟢 Aktif',
                        'draft'   => '📝 Draft',
                        'selesai' => '🏁 Selesai',
                        default   => $state,
                    }),

                TextColumn::make('candidates_count')
                    ->label('Kandidat')->counts('candidates')->badge()->color('warning'),

                TextColumn::make('votes_count')
                    ->label('Suara Masuk')->counts('votes')->badge()->color('success'),

                TextColumn::make('waktu_mulai')
                    ->label('Mulai')->dateTime('d M Y, H:i')->sortable(),

                TextColumn::make('waktu_selesai')
                    ->label('Selesai')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'aktif' => 'Aktif', 'selesai' => 'Selesai']),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('aktifkan')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn(Election $r) => $r->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan Pemilihan?')
                    ->modalDescription('Setelah diaktifkan, anggota dapat langsung memberikan suara.')
                    ->action(function (Election $record): void {
                        $record->update(['status' => 'aktif']);
                        Notification::make()->title('Pemilihan berhasil diaktifkan!')->success()->send();
                    }),

                // Tombol: Tutup pemilihan
                Action::make('tutup')
                    ->label('Tutup')
                    ->icon('heroicon-o-stop')
                    ->color('danger')
                    ->visible(fn(Election $r) => $r->status === 'aktif')
                    ->requiresConfirmation()
                    ->modalHeading('Tutup Pemilihan?')
                    ->modalDescription('Voting akan ditutup dan hasil final akan ditampilkan.')
                    ->action(function (Election $record): void {
                        $record->update(['status' => 'selesai']);
                        Notification::make()->title('Pemilihan telah ditutup!')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
