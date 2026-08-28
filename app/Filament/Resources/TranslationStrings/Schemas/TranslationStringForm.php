<?php

namespace App\Filament\Resources\TranslationStrings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class TranslationStringForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('key')
                            ->label('Key')
                            ->helperText('Used in code as t(\'key\'). Legacy keys (legacy.*) were auto-generated during migration — rename to something meaningful as you review each one.')
                            ->required(),
                        Select::make('group')
                            ->options([
                                'nav' => 'Navigation',
                                'footer' => 'Footer',
                                'forms' => 'Forms',
                                'global' => 'Global',
                                'press' => 'Press',
                                'contact' => 'Contact',
                                'legacy' => 'Legacy (needs review)',
                            ])
                            ->required()
                            ->default('global'),
                    ]),
                Textarea::make('fr')
                    ->label('French (canonical)')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('en')
                    ->label('English')
                    ->helperText('Leave blank if not yet translated — it shows as missing in the list rather than silently falling back to French.')
                    ->default(null)
                    ->rows(2)
                    ->columnSpanFull(),
                Toggle::make('suspect_source_language')
                    ->label('Suspect: the "French" value above might actually be English')
                    ->helperText('Flagged automatically during migration (TranslatePress sometimes scanned the English page and mislabeled the result). Check the French field, fix it if needed, then untick once confirmed correct.')
                    ->required(),
            ]);
    }
}