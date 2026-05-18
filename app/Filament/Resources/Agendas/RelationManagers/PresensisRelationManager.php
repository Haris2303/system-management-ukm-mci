<?php

namespace App\Filament\Resources\Agendas\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PresensisRelationManager extends RelationManager
{
    protected static string $relationship = 'presensis';

    protected static ?string $title = 'Daftar Presensi Anggota';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Presensi')
                ->columns(2)
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Nama Anggota')
                        ->icon('heroicon-m-user')
                        ->weight('semibold'),

                    TextEntry::make('status')
                        ->label('Status Kehadiran')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Hadir' => 'success',
                            'Absen' => 'danger',
                            default => 'gray',
                        }),

                    TextEntry::make('jam_hadir')
                        ->label('Jam Hadir')
                        ->icon('heroicon-m-clock')
                        ->dateTime('d F Y, H:i')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->weight('semibold'),

                TextColumn::make('user.divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Absen' => 'danger',
                        'Izin'  => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('jam_hadir')
                    ->label('Jam Hadir')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Absen' => 'Absen',
                        'Izin'  => 'Izin',
                    ]),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('status', 'asc')
            ->emptyStateHeading('Belum ada presensi')
            ->emptyStateDescription('Anggota dapat melakukan presensi melalui QR Code pada aplikasi mobile.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
