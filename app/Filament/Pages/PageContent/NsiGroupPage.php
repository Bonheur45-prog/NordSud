<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class NsiGroupPage extends EditsSinglePage
{
    protected static string $pageSlug = 'nsi-group';

    protected static string $pageTemplate = 'about';

    protected static string $defaultTitleFr = 'NSI Group';

    protected static ?string $navigationLabel = 'NSI Group';
    protected static string|\UnitEnum|null $navigationGroup = 'About';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static bool $shouldRegisterNavigation = true;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the NSI Group page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/nsi-group/hero'),
                ]),

            Section::make('Introduction')
                ->description('The centered text paragraphs shown below the hero banner.')
                ->schema([
                    Repeater::make('content.paragraphs')
                        ->label('Paragraphs')
                        ->schema([
                            TranslatedField::pairTextareaAtRoot('Paragraph', required: true, rows: 4),
                        ])
                        ->addActionLabel('Add paragraph')
                        ->collapsed()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? str($state['fr'])->limit(60)
                            : 'Paragraph')
                        ->minItems(1),
                ]),

            Section::make('Introduction Image')
                ->description('The centered image displayed beneath the introduction text.')
                ->schema([
                    FileUpload::make('content.body.image')
                        ->label('Image')
                        ->image()
                        ->directory('pages/nsi-group'),
                ]),
        ];
    }
}
