<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Foto')
                    ->schema([
                        FileUpload::make('foto')
                            ->label('Foto')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('gallery')
                            ->maxSize(1024)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail')
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),

                        Select::make('kategori')
                            ->label('Kategori')
                            ->options([
                                'Umum'      => 'Umum',
                                'Kegiatan'  => 'Kegiatan',
                                'Prestasi'  => 'Prestasi',
                                'Rapat'     => 'Rapat',
                                'Pelatihan' => 'Pelatihan',
                            ])
                            ->default('Umum')
                            ->required(),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Toggle::make('is_featured')
                            ->label('Tampilkan di Utama')
                            ->default(false),
                    ])->columns(2),
            ]);
    }
}
