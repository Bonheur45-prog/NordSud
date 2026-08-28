<?php

namespace App\Filament\Resources\TranslationStrings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TranslationStringsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->fontFamily('mono')
                    ->limit(40),
                TextColumn::make('group')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fr')
                    ->label('French')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('en')
                    ->label('English')
                    ->searchable()
                    ->limit(50)
                    ->placeholder('— missing —')
                    ->color(fn ($state) => filled($state) ? null : 'danger'),
                IconColumn::make('suspect_source_language')
                    ->label('Suspect')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('group')
            ->filters([
                SelectFilter::make('group')
                    ->options([
                        'nav' => 'Navigation',
                        'footer' => 'Footer',
                        'forms' => 'Forms',
                        'global' => 'Global',
                        'press' => 'Press',
                        'contact' => 'Contact',
                        'legacy' => 'Legacy (needs review)',
                    ]),
                TernaryFilter::make('missing_english')
                    ->label('English translation')
                    ->placeholder('All')
                    ->trueLabel('Missing')
                    ->falseLabel('Present')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('en'),
                        false: fn (Builder $query) => $query->whereNotNull('en'),
                    ),
                TernaryFilter::make('suspect_source_language')
                    ->label('Suspect source language')
                    ->placeholder('All')
                    ->trueLabel('Flagged as suspect')
                    ->falseLabel('Not flagged'),
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