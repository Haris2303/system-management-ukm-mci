<?php

namespace App\Filament\Resources\Materis\Schemas;

use App\Models\Divisi;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MateriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Materi')
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul Materi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Pengantar Laravel 12')
                            ->columnSpanFull(),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Jelaskan isi atau tujuan materi ini secara singkat.')
                            ->columnSpanFull(),

                        Select::make('divisi_id')
                            ->label('Divisi')
                            ->options(
                                fn() => auth()->user()->isKetuaDivisi()
                                    ? Divisi::where('id', auth()->user()->divisi_id)->pluck('nama', 'id')
                                    : Divisi::query()->orderBy('urut')->pluck('nama', 'id')
                            )
                            ->default(
                                fn() => auth()->user()->isKetuaDivisi()
                                    ? auth()->user()->divisi_id
                                    : null
                            )
                            ->disabled(fn() => auth()->user()->isKetuaDivisi())
                            ->dehydrated(true)
                            ->searchable()
                            ->placeholder(
                                fn() => auth()->user()->isKetuaDivisi()
                                    ? null
                                    : '— Materi Umum (untuk semua divisi) —'
                            )->options(function () {
                                $user = auth()->user();

                                if ($user?->isKetuaDivisi()) {
                                    return Divisi::where('id', $user->divisi_id)->pluck('nama', 'id');
                                }

                                if ($user?->hasRole('ketua_ukm')) {
                                    return []; // Ketua UKM tidak punya opsi divisi
                                }

                                return Divisi::query()->orderBy('urut')->pluck('nama', 'id');
                            })
                            ->default(function () {
                                $user = auth()->user();

                                if ($user?->isKetuaDivisi()) {
                                    return $user->divisi_id;
                                }

                                return null; // Default untuk Ketua UKM & Super Admin (Materi Umum)
                            })
                            ->disabled(function () {
                                $user = auth()->user();

                                // Kunci dropdown jika user adalah Ketua Divisi ATAU Ketua UKM
                                return $user?->isKetuaDivisi() || $user?->hasRole('ketua_ukm');
                            })
                            ->dehydrated(true)
                            ->searchable()
                            ->placeholder(function () {
                                $user = auth()->user();

                                if ($user?->isKetuaDivisi()) {
                                    return null;
                                }

                                return '— Materi Umum (untuk semua divisi) —';
                            })
                            ->helperText(function () {
                                $user = auth()->user();

                                if ($user?->isKetuaDivisi()) {
                                    return 'Materi otomatis dikaitkan ke divisi Anda.';
                                }

                                if ($user?->hasRole('ketua_ukm')) {
                                    return 'Sebagai Ketua UKM, Anda hanya diizinkan membuat Materi Umum.';
                                }

                                return 'Kosongkan jika materi ini untuk semua anggota.';
                            })
                            ->columnSpanFull(),

                    ])->columns(2),

                Section::make('Konten Materi')
                    ->description('Anda dapat mengisi salah satu atau keduanya: upload PDF dan/atau link eksternal.')
                    ->schema([

                        // ── FileUpload — hanya menerima PDF ──
                        FileUpload::make('file_path')
                            ->label('File Materi (PDF)')
                            ->disk('public')
                            ->directory('materi')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)                              // Maks 10 MB
                            ->helperText('Format yang diterima: PDF. Maksimal 10 MB.')
                            ->downloadable()
                            ->openable()
                            ->getUploadedFileNameForStorageUsing(
                                fn($file): string => Str::random(8) . '-'
                                    . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                                    . '.pdf'
                            )
                            ->columnSpanFull(),

                        TextInput::make('link_url')
                            ->label('Link Eksternal')
                            ->url()
                            ->maxLength(500)
                            ->prefixIcon('heroicon-o-link')
                            ->placeholder('https://youtube.com/... atau https://drive.google.com/...')
                            ->helperText('Link ke video, Google Drive, GitHub, atau sumber lainnya.')
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}
