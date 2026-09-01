<?php

namespace App\Filament\Resources;

use App\Models\PressPageSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PressPageSettingsResource extends Resource
{
    protected static ?string $model = PressPageSettings::class;

    protected static ?string $navigationLabel = 'Page Settings';

    protected static string|UnitEnum|null $navigationGroup = 'Press';

    protected static ?string $slug = 'press-page-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 100;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema([
                        FileUpload::make('hero_background_image')
                            ->label('Hero Section Background Image')
                            ->image()
                            ->directory('press')
                            ->visibility('public')
                            ->helperText('This image will be displayed as the background in the hero section of the Press index page.'),
                    ]),
                Grid::make(2)
                    ->schema([
                        TextInput::make('eyebrow_fr')
                            ->label('Eyebrow Text (French)')
                            ->placeholder('e.g., OUR PRESS'),
                        TextInput::make('eyebrow_en')
                            ->label('Eyebrow Text (English)')
                            ->placeholder('e.g., OUR PRESS'),
                    ]),
                Grid::make(2)
                    ->schema([
                        TextInput::make('heading_fr')
                            ->label('Main Heading (French)')
                            ->placeholder('e.g., Dernières actualités et publications'),
                        TextInput::make('heading_en')
                            ->label('Main Heading (English)')
                            ->placeholder('e.g., Latest News & Updates'),
                    ]),
                Grid::make(2)
                    ->schema([
                        TextInput::make('hero_subtitle_fr')
                            ->label('Hero Subtitle (French)')
                            ->helperText('Optional subtitle text below the hero title'),
                        TextInput::make('hero_subtitle_en')
                            ->label('Hero Subtitle (English)')
                            ->helperText('Optional subtitle text below the hero title'),
                    ]),
                Grid::make(2)
                    ->schema([
                        RichEditor::make('description_fr')
                            ->label('Section Description (French)')
                            ->columnSpanFull()
                            ->helperText('Rich text content displayed below the heading and above the posts grid'),
                        RichEditor::make('description_en')
                            ->label('Section Description (English)')
                            ->columnSpanFull()
                            ->helperText('Rich text content displayed below the heading and above the posts grid'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PressPageSettings\Pages\EditSettings::route('/'),
        ];
    }
}
