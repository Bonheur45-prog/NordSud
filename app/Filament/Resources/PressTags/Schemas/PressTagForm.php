<?php

namespace App\Filament\Resources\PressTags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PressTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name_fr')
                            ->label('Name (Français)')
                            ->required(),
                        TextInput::make('name_en')
                            ->label('Name (English)'),
                    ]),
                TextInput::make('slug')
                    ->helperText('Auto-generated from the French name if left blank. Used in the ?tag= filter URL.')
                    ->unique(ignoreRecord: true),
            ]);
    }
}
