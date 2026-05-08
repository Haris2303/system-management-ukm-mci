<?php

namespace App\Filament\Pages;

use App\Models\Agenda;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RekapPresensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Rekap Presensi';

    protected static ?string $title = 'Rekap Presensi Anggota';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Presensi';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.rekap-presensi';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'demisioner']))
                    ->withCount([
                        'presensis as hadir_count' => fn ($q) => $q->where('status', 'Hadir'),
                        'presensis as izin_count'  => fn ($q) => $q->where('status', 'Izin'),
                        'presensis as absen_count' => fn ($q) => $q->where('status', 'Absen'),
                        'presensis as total_presensi',
                    ])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user'),

                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('hadir_count')
                    ->label('Hadir')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('izin_count')
                    ->label('Izin')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('absen_count')
                    ->label('Absen')
                    ->badge()
                    ->color('danger')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('total_presensi')
                    ->label('Total Agenda')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('persentase')
                    ->label('% Kehadiran')
                    ->state(function (User $record): string {
                        if ($record->total_presensi === 0) return '—';
                        return round(($record->hadir_count / $record->total_presensi) * 100) . '%';
                    })
                    ->badge()
                    ->color(function (User $record): string {
                        if ($record->total_presensi === 0) return 'gray';
                        $pct = ($record->hadir_count / $record->total_presensi) * 100;
                        return match (true) {
                            $pct >= 80 => 'success',
                            $pct >= 60 => 'warning',
                            default    => 'danger',
                        };
                    })
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('agenda_id')
                    ->label('Filter Agenda')
                    ->placeholder('Semua Agenda')
                    ->options(fn () => Agenda::query()->pluck('nama_agenda', 'id')->toArray())
                    ->searchable()
                    ->modifyQueryUsing(function (Builder $query, array $state): Builder {
                        $agendaId = $state['value'] ?? null;
                        if (! $agendaId) {
                            return $query;
                        }

                        return $query->withCount([
                            'presensis as hadir_count'    => fn ($q) => $q->where('agenda_id', $agendaId)->where('status', 'Hadir'),
                            'presensis as izin_count'     => fn ($q) => $q->where('agenda_id', $agendaId)->where('status', 'Izin'),
                            'presensis as absen_count'    => fn ($q) => $q->where('agenda_id', $agendaId)->where('status', 'Absen'),
                            'presensis as total_presensi' => fn ($q) => $q->where('agenda_id', $agendaId),
                        ]);
                    }),
            ])
            ->defaultSort('hadir_count', 'desc')
            ->poll('30s')
            ->emptyStateHeading('Belum ada anggota aktif')
            ->emptyStateIcon('heroicon-o-users');
    }
}
