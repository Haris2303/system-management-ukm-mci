<?php

namespace App\Filament\Pages;

use App\Models\OpenRecruitment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class OpenRecruitmentPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Megaphone;

    protected static ?string $navigationLabel = 'Open Recruitment';

    protected static ?string $title = 'Pengaturan Open Recruitment';

    protected static string|UnitEnum|null $navigationGroup = 'Rekrutmen';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.open-recruitment';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'ketua_ukm']) ?? false;
    }

    public function mount(): void
    {
        $record = OpenRecruitment::firstOrNew(['id' => 1]);
        $this->data = $record->toArray();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status Rekrutmen')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Buka Pendaftaran')
                            ->helperText('Aktifkan untuk membuka pendaftaran di web publik.')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(false)
                            ->columnSpanFull(),

                        TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Open Recruitment 2025'),

                        TextInput::make('gelombang')
                            ->label('Gelombang / Batch')
                            ->maxLength(100)
                            ->placeholder('Gelombang 1'),
                    ])->columns(2),

                Section::make('Jadwal Pendaftaran')
                    ->schema([
                        DateTimePicker::make('waktu_mulai')
                            ->label('Dibuka Mulai')
                            ->required()
                            ->seconds(false),

                        DateTimePicker::make('waktu_selesai')
                            ->label('Ditutup Pada')
                            ->required()
                            ->seconds(false)
                            ->after('waktu_mulai'),
                    ])->columns(2),

                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Ditampilkan di halaman publik.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        OpenRecruitment::updateOrCreate(['id' => 1], $data);

        Notification::make()
            ->title('Pengaturan rekrutmen berhasil disimpan')
            ->success()
            ->send();
    }
}
