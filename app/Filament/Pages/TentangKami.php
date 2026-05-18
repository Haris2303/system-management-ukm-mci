<?php

namespace App\Filament\Pages;

use App\Models\ProfilUkm;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class TentangKami extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::InformationCircle;

    protected static ?string $navigationLabel = 'Tentang Kami';

    protected static ?string $title = 'Manajemen Konten — Tentang Kami';

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.tentang-kami';

    public ?array $data = [];

    public function mount(): void
    {
        $profil = ProfilUkm::first() ?? new ProfilUkm();
        $this->data = $profil->toArray();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas UKM')
                    ->schema([
                        TextInput::make('nama_ukm')
                            ->label('Nama UKM')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('tagline')
                            ->label('Tagline / Slogan')
                            ->maxLength(255)
                            ->placeholder('Komunitas yang Menginspirasi'),
                    ])->columns(2),

                Section::make('Deskripsi')
                    ->schema([
                        Textarea::make('deskripsi')
                            ->label('Deskripsi UKM')
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('visi')
                            ->label('Visi')
                            ->rows(3),

                        Textarea::make('misi')
                            ->label('Misi')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Keunggulan / Highlight')
                    ->description('Ikon menggunakan kelas Font Awesome, contoh: fa-solid fa-circle-check')
                    ->schema([
                        Repeater::make('keunggulan')
                            ->label('')
                            ->schema([
                                Grid::make(12)->schema([
                                    TextInput::make('icon')
                                        ->label('Ikon Font Awesome')
                                        ->placeholder('fa-solid fa-circle-check')

                                        ->maxLength(60)
                                        ->columnSpan(4),

                                    TextInput::make('teks')
                                        ->label('Teks')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Workshop & Pelatihan rutin setiap bulan')
                                        ->columnSpan(8),
                                ]),
                            ])
                            ->addActionLabel('Tambah Poin')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        ProfilUkm::updateOrCreate(['id' => 1], $data);

        Notification::make()
            ->title('Berhasil disimpan')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save')
                ->icon(Heroicon::Check),
        ];
    }
}
