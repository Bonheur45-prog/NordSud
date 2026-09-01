<?php

namespace App\Filament\Resources\PressPosts\Schemas;

use App\Models\PressTag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PressPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->helperText('Shared across languages, e.g. "nsi-group-kenya".'),
                    ]),
                Grid::make(2)
                    ->schema([
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
                Grid::make(1)
                    ->schema([
                        Select::make('author_id')
                            ->label('Author')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                TextInput::make('role_fr')->label('Role (Français)'),
                                TextInput::make('role_en')->label('Role (English)'),
                            ])
                            ->helperText('Managed under Press Authors in the sidebar.'),
                    ]),
                Grid::make(2)
                    ->schema([
                        Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name_fr')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (PressTag $tag): string => $tag->name_en
                                ? "{$tag->name_fr} / {$tag->name_en}"
                                : $tag->name_fr)
                            ->helperText('Drives both the on-page tag chips and the "Popular Tags" sidebar.'),
                        Toggle::make('is_featured')
                            ->label('Show in "Popular Posts" sidebar')
                            ->helperText('Curated by hand rather than auto-ranked by views.')
                            ->inline(false),
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
                            ->helperText('Shown on listing cards and search results. Falls back to a trimmed excerpt of the body when left blank.')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->required()
                            ->helperText('Use the quote block in the toolbar for a pull-quote — it renders as a highlighted callout on the article page.')
                            ->columnSpanFull(),
                    ])
                    ->maxItems(2)
                    ->defaultItems(0)
                    ->addActionLabel('Add translation (French / English)')
                    ->columnSpanFull(),
            ]);
    }
}
