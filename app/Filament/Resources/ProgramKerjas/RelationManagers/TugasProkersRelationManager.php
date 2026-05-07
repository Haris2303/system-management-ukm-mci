<?php

namespace App\Filament\Resources\ProgramKerjas\RelationManagers;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TugasProkersRelationManager extends RelationManager
{
    protected static string $relationship = 'tugasProkers';

    protected static ?string $title = 'Daftar Tugas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_tugas')
                    ->label('Nama Tugas')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->placeholder('Contoh: Booking ruangan untuk acara'),

                TextInput::make('urut')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_selesai')
                    ->label('Sudah Selesai')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_tugas')
            ->columns([
                TextColumn::make('urut')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                TextColumn::make('nama_tugas')
                    ->label('Nama Tugas')
                    ->searchable()
                    ->wrap()
                    ->weight('semibold'),

                ToggleColumn::make('is_selesai')
                    ->label('Selesai?')
                    ->onColor('success')
                    ->offColor('gray')
                    ->afterStateUpdated(
                        fn($livewire) => $livewire->dispatch('proker-progress-updated')
                    ),

                TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->dateTime('d M Y, H:i')
                    ->since()
                    ->toggleable(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Tugas')
                    ->after(fn($livewire) => $livewire->dispatch('proker-progress-updated')),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(fn($livewire) => $livewire->dispatch('proker-progress-updated')),
                DeleteAction::make()
                    ->after(fn($livewire) => $livewire->dispatch('proker-progress-updated')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('tandai_selesai')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records, $livewire): void {
                            $records->each->update(['is_selesai' => true]);
                            $livewire->dispatch('proker-progress-updated');
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('tandai_belum')
                        ->label('Tandai Belum Selesai')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(function ($records, $livewire): void {
                            $records->each->update(['is_selesai' => false]);
                            $livewire->dispatch('proker-progress-updated');
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->after(fn($livewire) => $livewire->dispatch('proker-progress-updated')),
                ]),
            ])
            ->reorderable('urut')
            ->defaultSort('urut');
    }
}
