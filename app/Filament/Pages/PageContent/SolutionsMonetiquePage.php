<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class SolutionsMonetiquePage extends EditsSinglePage
{
    protected static string $pageSlug = 'solutions-monetiques';

    protected static string $pageTemplate = 'solution';

    protected static string $defaultTitleFr = 'Solutions Monétiques';

    protected static ?string $navigationLabel = 'Solutions Monétiques';
    protected static string|\UnitEnum|null $navigationGroup = 'Solutions';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static bool $shouldRegisterNavigation = true;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the Solutions Monétiques page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/solutions-monetiques/hero'),
                ]),

            Section::make('Introduction')
                ->description('The centered introduction shown below the hero banner.')
                ->schema([
                    TranslatedField::pairTextarea(
                        'content.introduction',
                        'Introduction',
                        required: true,
                        rows: 3,
                    ),
                ]),

            Section::make('Solution Sections')
                ->description('The alternating image and text sections shown down the page.')
                ->schema([
                    Repeater::make('content.sections')
                        ->label('Solutions')
                        ->schema([
                            TranslatedField::pair('title', 'Section title', required: true),
                            Repeater::make('bullets')
                                ->label('Bullet points')
                                ->schema([
                                    TranslatedField::pairTextareaAtRoot('Bullet point', required: true, rows: 2),
                                ])
                                ->addActionLabel('Add bullet point')
                                ->collapsible()
                                ->reorderable()
                                ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                                    ? str($state['fr'])->limit(60)
                                    : 'Bullet point')
                                ->minItems(1),
                            FileUpload::make('image')
                                ->label('Section image')
                                ->image()
                                ->directory('pages/solutions-monetiques/sections')
                                ->visibility('public'),
                        ])
                        ->addActionLabel('Add solution section')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['title']['fr'] ?? null)
                            ? str($state['title']['fr'])->limit(60)
                            : 'Solution section')
                        ->minItems(1),
                ]),
        ];
    }
}
