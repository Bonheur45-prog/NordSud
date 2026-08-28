<?php

namespace App\Filament\Resources\GlossaryTerms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class GlossaryTermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('term_fr')
                            ->label('French term (canonical)')
                            ->required(),
                        TextInput::make('term_en')
                            ->label('English term')
                            ->required(),
                    ]),
                Textarea::make('notes')
                    ->label('Usage notes')
                    ->helperText('Context for whoever is translating — e.g. which pages use this term, or nuances to preserve.')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}