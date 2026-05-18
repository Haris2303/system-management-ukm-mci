<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Kolom kiri (konten utama) ─────────────────
                Group::make()->schema([

                    Section::make('Thumbnail')->schema([

                        FileUpload::make('thumbnail')
                            ->label('Gambar Cover')
                            ->image()
                            ->disk('public')
                            ->directory('posts/thumbnails')
                            ->automaticallyResizeImagesMode('cover')
                            ->automaticallyCropImagesToAspectRatio('16:9')
                            ->automaticallyResizeImagesToWidth('1200')
                            ->automaticallyResizeImagesToHeight('675')
                            ->helperText('Rasio 16:9. Rekomendasi: 1200×675px.'),
                    ])->columnSpanFull(),

                    Section::make('Konten Berita')->schema([

                        TextInput::make('judul')
                            ->label('Judul Berita')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 600)
                            ->afterStateUpdated(
                                fn(Set $set, ?string $state) =>
                                $set('slug', Str::slug($state ?? ''))
                            )
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefix('berita/')
                            ->columnSpanFull(),

                        Textarea::make('ringkasan')
                            ->label('Ringkasan / Excerpt')
                            ->helperText('Ditampilkan di kartu berita pada landing page. Maks 250 karakter.')
                            ->maxLength(250)
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('konten')
                            ->label('Isi Artikel')
                            ->required()
                            ->extraInputAttributes(['style' => 'min-height: 100px'])
                            ->toolbarButtons([
                                'h1',
                                'h2',
                                'h3',
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'link',
                                'attachFiles',
                                'undo',
                                'redo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->columnSpanFull(),

                    ]),

                ])->columnSpanFull(),

                // ── Kolom kanan (metadata) ────────────────────
                Grid::make(2)->schema([

                    Section::make('Kategori & Tag')->schema([

                        Select::make('kategori')
                            ->label('Kategori')
                            ->options([
                                'Berita'      => '📰 Berita',
                                'Kegiatan'    => '📅 Kegiatan',
                                'Prestasi'    => '🏆 Prestasi',
                                'Pengumuman'  => '📢 Pengumuman',
                            ])
                            ->default('Berita')
                            ->required(),

                        TagsInput::make('tag')
                            ->label('Tag')
                            ->placeholder('Tambah tag...')
                            ->helperText('Tekan Enter atau koma untuk menambah tag.'),
                    ])->columns(1),

                    Section::make('Publikasi')->schema([

                        Select::make('status')
                            ->label('Status')
                            ->options(['draft' => '📝 Draft', 'published' => '🟢 Publish'])
                            ->default('draft')
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->seconds(false)
                            ->helperText('Kosongkan untuk otomatis saat publish.'),

                        Toggle::make('is_featured')
                            ->label('Jadikan Featured')
                            ->helperText('Tampil di posisi utama halaman berita.'),
                    ])->columns(1),

                ])->columnSpanFull(),

            ]);
    }
}
