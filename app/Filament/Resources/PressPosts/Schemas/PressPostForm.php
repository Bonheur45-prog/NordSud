<?php

namespace App\Filament\Resources\PressPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PressPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->helperText('Shared across languages, e.g. "nsi-group-kenya".'),
                        Select::make('type')
                            ->options([
                                'press' => 'Press article',
                                'job' => 'Job posting',
                            ])
                            ->required()
                            ->default('press'),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->required()
                            ->default('draft'),
                    ]),
                FileUpload::make('featured_image')
                    ->image()
                    ->directory('press')
                    ->visibility('public'),
                DateTimePicker::make('published_at')
                    ->helperText('Only published posts with a date in the past appear on the public site.'),

                Repeater::make('translations')
                    ->relationship()
                    ->label('Translations')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('locale')
                                    ->options([
                                        'fr' => 'French',
                                        'en' => 'English',
                                    ])
                                    ->required(),
                                TextInput::make('title')
                                    ->required(),
                            ]),
                        TextInput::make('excerpt')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                    ])
                    ->maxItems(2)
                    ->defaultItems(0)
                    ->addActionLabel('Add translation (French / English)')
                    ->columnSpanFull(),
            ]);
    }
}