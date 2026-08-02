<?php

namespace App\Filament\Widgets;

use App\Models\Divisi;
use App\Models\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AnggotaAktifWidget extends BaseWidget
{
    /**
     * Tampil di baris ketiga dashboard, tepat di bawah Grafik Transaksi Kas
     * (sort 0, span 2) — sejajar lebar dengan grafik tersebut.
     */
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 2;

    protected static ?string $heading = 'Anggota Aktif';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::role('anggota')
                    ->whereNull('kicked_at')
                    ->with('divisi')
                    ->orderBy('name')
            )
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->getStateUsing(
                        fn(User $r) => $r->avatar_url
                            ?? 'https://ui-avatars.com/api/?name=' . urlencode($r->name) . '&background=1a4ff5&color=fff&size=64'
                    )
                    ->circular()
                    ->width(32)
                    ->height(32),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('divisi_id')
                    ->label('Divisi')
                    ->options(Divisi::orderBy('urut')->pluck('nama', 'id')),
            ])
            ->paginated([10, 25])
            ->striped();
    }
}
