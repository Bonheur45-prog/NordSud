<?php

namespace App\Filament\Resources\PressPosts\Tables;

use App\Models\PressPost;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PressPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label(''),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => $state === 'job' ? 'info' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'job' ? 'Job' : 'Press'),
                TextColumn::make('french_title')
                    ->label('French title')
                    ->getStateUsing(fn (PressPost $record) => $record->translation('fr')?->title)
                    ->placeholder('— missing —')
                    ->color(fn ($state) => filled($state) ? null : 'danger'),
                TextColumn::make('english_title')
                    ->label('English title')
                    ->getStateUsing(fn (PressPost $record) => $record->translations->firstWhere('locale', 'en')?->title)
                    ->placeholder('— missing —')
                    ->color(fn ($state) => filled($state) ? null : 'danger'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'press' => 'Press article',
                        'job' => 'Job posting',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}