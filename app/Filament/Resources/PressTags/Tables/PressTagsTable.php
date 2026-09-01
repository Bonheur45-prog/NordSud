<?php

namespace App\Filament\Resources\PressTags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PressTagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_fr')
                    ->label('Name (FR)')
                    ->searchable(),
                TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable(),
                TextColumn::make('slug'),
                TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Used on'),
            ])
            ->defaultSort('name_fr')
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
