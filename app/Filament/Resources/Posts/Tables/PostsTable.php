<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->disk('public')
                    ->width(72)->height(44)
                    ->defaultImageUrl(fn() => 'https://placehold.co/72x44/dbeafe/1d4ed8?text=MCI')
                    ->extraImgAttributes(['style' => 'border-radius:8px;object-fit:cover;']),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()->sortable()->weight('bold')
                    ->description(fn(Post $r) => Carbon::parse($r->published_at)->format('d M Y'), 20)
                    ->limit(30),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'Prestasi'   => 'warning',
                        'Kegiatan'   => 'success',
                        'Pengumuman' => 'danger',
                        default      => 'primary',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn(string $state) => $state === 'published' ? '🟢 Tayang' : '📝 Draft'),

                IconColumn::make('is_featured')->label('Featured')->boolean(),

                TextColumn::make('views')->label('Views')->numeric()->sortable(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
                SelectFilter::make('kategori')
                    ->options(['Berita' => 'Berita', 'Kegiatan' => 'Kegiatan', 'Prestasi' => 'Prestasi', 'Pengumuman' => 'Pengumuman']),
                TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn(Post $r) => route('berita.show', $r->slug))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }
}
