<?php

namespace App\Filament\Resources\TransaksiKas\Tables;

use App\Models\TransaksiKas;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransaksiKasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state) => $state === 'masuk' ? 'success' : 'danger')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'masuk'  => '⬆️ Masuk',
                        'keluar' => '⬇️ Keluar',
                        default  => $state,
                    }),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id_ID')
                    ->color(fn(TransaksiKas $r) => $r->jenis === 'masuk' ? 'success' : 'danger')
                    ->weight('bold')
                    ->formatStateUsing(function (TransaksiKas $record): string {
                        $prefix = $record->jenis === 'masuk' ? '+ ' : '− ';
                        return $prefix . 'Rp ' . number_format($record->nominal, 0, ',', '.');
                    }),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('pencatat.name')
                    ->label('Dicatat oleh')
                    ->placeholder('—')
                    ->toggleable(),

                ImageColumn::make('bukti')
                    ->label('Bukti')
                    ->disk('public')
                    ->circular()
                    ->size(36)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('jenis')
                    ->label('Jenis Transaksi')
                    ->options([
                        'masuk'  => '⬆️ Kas Masuk',
                        'keluar' => '⬇️ Kas Keluar',
                    ]),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn($q, $d) => $q->whereDate('tanggal', '>=', $d))
                            ->when($data['sampai_tanggal'], fn($q, $d) => $q->whereDate('tanggal', '<=', $d));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('tanggal', 'desc')->striped();
    }
}
