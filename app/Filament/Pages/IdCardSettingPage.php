<?php

namespace App\Filament\Pages;

use App\Models\IdCardSetting;
use App\Models\User;
use App\Support\IdCardTemplates;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class IdCardSettingPage extends Page
{
    protected static BackedEnum|string|null $navigationIcon  = Heroicon::OutlinedIdentification;
    protected static ?string               $navigationLabel = 'Template ID Card';
    protected static ?string               $title           = 'Pengaturan Template ID Card';
    protected static ?int                  $navigationSort  = 90;
    protected string                       $view            = 'filament.pages.id-card-setting';

    public string  $activeTemplate = '';
    public ?string $backgroundImage = null;

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->activeTemplate  = IdCardSetting::activeTemplate();
        $this->backgroundImage = IdCardSetting::backgroundImageUrl();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadBackground')
                ->label('Ganti Background Image')
                ->icon(Heroicon::OutlinedPhoto)
                ->color('gray')
                ->slideOver()
                ->form([
                    FileUpload::make('background_image')
                        ->label('Gambar Background ID Card')
                        ->image()
                        ->disk('public')
                        ->directory('id-card-backgrounds')
                        ->imageEditor()
                        ->maxSize(5 * 1024)
                        ->helperText(
                            'Format JPG/PNG. Gunakan rasio 3:5 (misal 600×1000px) untuk hasil terbaik. '
                            . 'Desain di Canva/Figma/Photoshop lalu upload di sini. '
                            . 'Background image akan menggantikan template warna.'
                        ),
                ])
                ->action(function (array $data): void {
                    if (!empty($data['background_image'])) {
                        IdCardSetting::setBackgroundImage($data['background_image']);
                        $this->backgroundImage = IdCardSetting::backgroundImageUrl();
                        Notification::make()->title('Background ID Card diperbarui')->success()->send();
                    }
                }),

            Action::make('removeBackground')
                ->label('Hapus Background')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Hapus Background Image?')
                ->modalDescription('ID Card akan kembali menggunakan template warna yang dipilih.')
                ->action(function (): void {
                    IdCardSetting::setBackgroundImage(null);
                    $this->backgroundImage = null;
                    Notification::make()->title('Background dihapus')->warning()->send();
                })
                ->visible(fn (): bool => (bool) $this->backgroundImage),
        ];
    }

    public function selectTemplate(string $slug): void
    {
        IdCardSetting::setTemplate($slug);
        $this->activeTemplate = $slug;

        $label = IdCardTemplates::find($slug)['label'];
        Notification::make()
            ->title("Template \"{$label}\" diaktifkan")
            ->success()
            ->send();
    }

    public function getTemplates(): array
    {
        return IdCardTemplates::all();
    }
}
