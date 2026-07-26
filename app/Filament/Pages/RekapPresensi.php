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

    public static function canAccess(): bool
    {
        // Bendahara hanya boleh mengakses fitur keuangan (E-Kas Keuangan);
        // ketua_ukm dibatasi hanya E-Voting, Open Recruitment, Divisi &
        // Laporan Keuangan E-Kas.
        return ! (auth()->user()?->hasAnyRole(['bendahara', 'ketua_ukm']) ?? false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                // Ambil nilai filter "Agenda" langsung dari state filter tabel,
                // supaya withCount di bawah bisa dihitung ulang khusus untuk
                // agenda yang dipilih (bukan menumpuk withCount dengan alias
                // yang sama, yang perilakunya tidak konsisten antar driver DB).
                $agendaId = $this->getTableFilterState('agenda_id')['value'] ?? null;

                return User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'demisioner']))
                    ->withCount([
                        'presensis as hadir_count' => fn ($q) => $q
                            ->when($agendaId, fn ($q) => $q->where('agenda_id', $agendaId))
                            ->where('status', 'Hadir'),
                        'presensis as izin_count' => fn ($q) => $q
                            ->when($agendaId, fn ($q) => $q->where('agenda_id', $agendaId))
                            ->where('status', 'Izin'),
                        'presensis as absen_count' => fn ($q) => $q
                            ->when($agendaId, fn ($q) => $q->where('agenda_id', $agendaId))
                            ->where('status', 'Absen'),
                        'presensis as total_presensi' => fn ($q) => $q
                            ->when($agendaId, fn ($q) => $q->where('agenda_id', $agendaId)),
                    ]);
            })
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
                    // Query dasar sudah membaca state filter ini langsung
                    // (lihat ->query() di atas), jadi di sini cukup no-op
                    // supaya Filament tidak mencoba where('agenda_id', ...)
                    // pada tabel users (kolom itu tidak ada di sana).
                    ->query(fn (Builder $query): Builder => $query),
            ])
            ->defaultSort('hadir_count', 'desc')
            ->poll('30s')
            ->emptyStateHeading('Belum ada anggota aktif')
            ->emptyStateIcon('heroicon-o-users');
    }
}
