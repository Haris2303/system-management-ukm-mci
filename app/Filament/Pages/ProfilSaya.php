<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilSaya extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?string $title = 'Profil Saya';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.profil-saya';

    public ?array $data = [];

    public string $selectedAvatar = '';

    /** Path foto yang sudah pernah diupload — tetap tersedia sebagai opsi meski user ganti ke emoji */
    public string $uploadedPhotoPath = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->selectedAvatar    = $user->avatar ?? '';
        $this->uploadedPhotoPath = $user->last_photo_path ?? '';

        $this->data = [
            'name'  => $user->name,
            'email' => $user->email,
            'no_hp' => $user->no_hp ?? '',
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Foto Profil')
                    ->description(function () {
                        $endsAt = auth()->user()->photoUploadCooldownEndsAt();
                        if ($endsAt) {
                            $days = (int) now()->diffInDays($endsAt, false) + 1;
                            return "Foto profil baru dapat diupload setelah {$endsAt->translatedFormat('d F Y')} ({$days} hari lagi). Cooldown berlaku 2 minggu setelah upload terakhir.";
                        }
                        return 'Upload foto profil, atau pilih avatar emoji di bawah. Setelah upload, foto tidak dapat diganti selama 2 minggu.';
                    })
                    ->schema([
                        FileUpload::make('avatar_file')
                            ->label('Upload Foto')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->disabled(fn () => ! auth()->user()->canUploadPhoto())
                            ->helperText(fn () => auth()->user()->canUploadPhoto()
                                ? 'Format JPG, PNG, atau WebP. Maks 2MB.'
                                : 'Masih dalam periode cooldown.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Akun')
                    ->description(fn() => ! auth()->user()->isSuperAdmin()
                        ? 'Informasi akun hanya dapat diubah oleh Super Admin.'
                        : null
                    )
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn() => ! auth()->user()->isSuperAdmin())
                            ->dehydrated(fn() => auth()->user()->isSuperAdmin())
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn() => ! auth()->user()->isSuperAdmin())
                            ->dehydrated(fn() => auth()->user()->isSuperAdmin())
                            ->rules([fn() => Rule::unique('users', 'email')->ignore(auth()->id())]),

                        TextInput::make('no_hp')
                            ->label('No. HP / WhatsApp')
                            ->tel()
                            ->maxLength(20)
                            ->disabled(fn() => ! auth()->user()->isSuperAdmin())
                            ->dehydrated(fn() => auth()->user()->isSuperAdmin()),
                    ])->columns(2),

                Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password.')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state)),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->dehydrated(false),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function selectAvatar(int $index): void
    {
        $presets = $this->getPresetAvatars();
        if (isset($presets[$index])) {
            $p = $presets[$index];
            $this->selectedAvatar = "emoji:{$p['emoji']}:{$p['bg']}";
            $this->data['avatar_file'] = null;
        }
    }

    /** Kembali ke foto yang pernah diupload tanpa harus upload ulang */
    public function selectUploadedPhoto(): void
    {
        $this->selectedAvatar = $this->uploadedPhotoPath;
        $this->data['avatar_file'] = null;
    }

    public function getPresetAvatars(): array
    {
        $colors = ['ffd5dc', 'ffdfbf', 'b6e3f4', 'c0aede', 'd1d4f9', 'e2e8f0', 'bbf7d0', 'fef9c3', 'fce7f3', 'e0f2fe', 'f3e8ff', 'dcfce7'];

        $emojis = [
            // Peliharaan & umum
            '🐱', '🐶', '🐰', '🐹', '🐭', '🐮',
            // Satwa liar
            '🦊', '🐺', '🐻', '🐼', '🦁', '🐯',
            // Burung
            '🐧', '🦉', '🦜', '🦚', '🦅', '🦆',
            // Laut
            '🐬', '🦈', '🐳', '🐠', '🦑', '🐙',
            // Eksotis
            '🦒', '🦓', '🐘', '🦔', '🐿️', '🦦',
            // Fantasi & reptil
            '🦄', '🐲', '🦖', '🦕', '🐸', '🐊',
            // Lainnya
            '🦋', '🐝', '🦎', '🐢', '🐒', '🦍',
        ];

        return array_map(
            fn($i, $emoji) => ['emoji' => $emoji, 'bg' => $colors[$i % count($colors)]],
            array_keys($emojis),
            $emojis
        );
    }

    public function getCurrentAvatarDisplay(): array
    {
        if (!$this->selectedAvatar) {
            return ['type' => 'initials'];
        }

        if (str_starts_with($this->selectedAvatar, 'emoji:')) {
            [, $emoji, $color] = explode(':', $this->selectedAvatar, 3);
            return ['type' => 'emoji', 'emoji' => $emoji, 'bg' => $color];
        }

        $url = str_starts_with($this->selectedAvatar, 'http')
            ? $this->selectedAvatar
            : asset('storage/' . $this->selectedAvatar);

        return ['type' => 'img', 'url' => $url];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $avatarFile = $data['avatar_file'] ?? null;
        unset($data['avatar_file']);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($avatarFile) {
            if (! $user->canUploadPhoto()) {
                $endsAt = $user->photoUploadCooldownEndsAt();
                Notification::make()
                    ->title('Tidak dapat upload foto')
                    ->body("Foto profil baru dapat diupload setelah {$endsAt->translatedFormat('d F Y')}.")
                    ->danger()
                    ->send();
                return;
            }

            $data['avatar']            = $avatarFile;
            $data['last_photo_path']   = $avatarFile;
            $data['photo_uploaded_at'] = now();
            $this->selectedAvatar      = $avatarFile;
            $this->uploadedPhotoPath   = $avatarFile;
        } elseif ($this->selectedAvatar) {
            $data['avatar'] = $this->selectedAvatar;
        }

        $user->update($data);

        Notification::make()
            ->title('Profil berhasil diperbarui')
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
