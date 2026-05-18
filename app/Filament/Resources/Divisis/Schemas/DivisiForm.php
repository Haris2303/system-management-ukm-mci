<?php

namespace App\Filament\Resources\Divisis\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DivisiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Divisi')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Divisi')
                            ->required()
                            ->maxLength(100)
                            ->live(debounce: 500)
                            ->afterStateUpdated(
                                fn(Set $set, ?string $state) =>
                                $set('slug', Str::slug($state ?? ''))
                            ),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        TextInput::make('icon')
                            ->label('Ikon (Emoji)')
                            ->default('💻')
                            ->helperText('Masukkan satu emoji. Contoh: 💻 🤖 🎨 🔒 ☁️'),

                        TextInput::make('urut')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),

                        TextInput::make('ketua')
                            ->label('Nama Ketua Divisi')
                            ->maxLength(255)
                            ->placeholder('Opsional'),

                        Toggle::make('is_active')
                            ->label('Buka Pendaftaran Divisi Ini')
                            ->helperText('Nonaktifkan agar divisi ini tidak muncul di form pendaftaran.')
                            ->default(true),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi Divisi')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                    ])->columns(2),
            ]);
    }
}
