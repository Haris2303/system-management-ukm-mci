<?php

namespace App\Filament\Resources\RagDocuments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RagDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Upload Dokumen PDF')
                    ->description('Upload dokumen PDF berisi informasi UKM MCI. Chatbot akan belajar dari dokumen ini.')
                    ->schema([
                        FileUpload::make('path_file')
                            ->label('File PDF')
                            ->disk('local')
                            ->directory('rag-docs')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(20480)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('nama_file')
                            ->label('Nama Dokumen')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi Konten')
                            ->placeholder('Contoh: Panduan lengkap UKM MCI 2025, berisi informasi program, divisi, dan pendaftaran.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
