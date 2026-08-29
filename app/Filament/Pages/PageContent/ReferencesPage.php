<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ReferencesPage extends EditsSinglePage
{
    protected static string $pageSlug = 'references';

    protected static string $pageTemplate = 'references';

    protected static string $defaultTitleFr = 'Références';

    protected static ?string $navigationLabel = 'References';
    protected static string|\UnitEnum|null $navigationGroup = 'About';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static bool $shouldRegisterNavigation = true;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the References page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/references/hero'),
                ]),

            Section::make('Introduction')
                ->description('The short centered message shown above the client logo wall.')
                ->schema([
                    TranslatedField::pairTextarea(
                        'content.introduction',
                        'Introduction',
                        required: true,
                        rows: 3,
                    ),
                ]),

            Section::make('Client Reference Logos')
                ->description('The logo wall is managed separately so the same references can also appear on the Home page.')
                ->schema([]),
        ];
    }
}
