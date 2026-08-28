<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('title_fr')
                            ->label('Partner name (Français)')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('title_en')
                            ->label('Partner name (English)')
                            ->helperText('Usually the same as the French name for a brand/company name.'),
                    ]),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText("Used in the page URL. Auto-filled from the name — only change if you know what you're doing."),
                FileUpload::make('content.logo')
                    ->label('Logo')
                    ->image()
                    ->directory('partners/logos')
                    ->visibility('public')
                    ->helperText("Shown on the partner's page and anywhere partner logos are listed."),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->required()
                    ->default('draft'),
                TextInput::make('order')
                    ->label('Display order')
                    ->numeric()
                    ->required()
                    ->default(0),

                Section::make('Paragraphs')
                    ->description('The main paragraphs describing this partner — e.g. what they do and their relationship with NSI Group.')
                    ->schema([
                        Repeater::make('content.paragraphs')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Textarea::make('fr')
                                            ->label('Paragraph (Français)')
                                            ->required()
                                            ->rows(3),
                                        Textarea::make('en')
                                            ->label('Paragraph (English)')
                                            ->rows(3)
                                            ->helperText('Leave blank if not yet translated — falls back to French on the live site.'),
                                    ]),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add paragraph')
                            ->collapsible()
                            ->reorderable()
                            ->itemLabel(fn (array $state) => (string) str($state['fr'] ?? 'Paragraph')->limit(50)),
                    ]),
            ]);
    }
}
