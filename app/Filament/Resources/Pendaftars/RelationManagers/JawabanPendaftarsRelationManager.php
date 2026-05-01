<?php

namespace App\Filament\Resources\Pendaftars\RelationManagers;

use App\Models\JawabanPendaftar;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class JawabanPendaftarsRelationManager extends RelationManager
{
    protected static string $relationship = 'jawabanPendaftars';

    protected static ?string $title = 'Jawaban & Penilaian';

    // ═══════════════════════════════════════════════════════════
    // FORM (hanya field nilai_skor yang bisa diedit Ketua Divisi)
    // ═══════════════════════════════════════════════════════════

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nilai_skor')
                    ->label('Nilai Skor')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('/ 100')
                    ->helperText('Berikan skor 0–100 berdasarkan kualitas jawaban.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jawaban_teks')
            ->columns([
                // Nomor urut pertanyaan
                TextColumn::make('pertanyaan.urut')
                    ->label('No.')
                    ->sortable()
                    ->width(50),

                // Teks pertanyaan
                TextColumn::make('pertanyaan.pertanyaan_teks')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->weight('semibold')
                    ->color('gray'),

                // Jawaban pendaftar (ini inti dari RelationManager)
                TextColumn::make('jawaban_teks')
                    ->label('Jawaban Pendaftar')
                    ->wrap()
                    ->limit(200)
                    ->searchable(),

                // Nilai skor yang bisa langsung diedit secara inline
                TextInputColumn::make('nilai_skor')
                    ->label('Nilai Skor (0–100)')
                    ->type('number')
                    ->rules(['nullable', 'integer', 'min:0', 'max:100'])
                    ->afterStateUpdated(function (JawabanPendaftar $record, $state): void {
                        Notification::make()
                            ->title("Skor disimpan: {$state}")
                            ->success()
                            ->duration(2000)
                            ->send();
                    }),
            ])->defaultSort('id', 'asc')
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([
                EditAction::make()
                    ->label('Beri Nilai')
                    ->icon('heroicon-o-star')
                    ->color('warning'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
