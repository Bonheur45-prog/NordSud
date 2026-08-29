<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ConsultingAdvisoryPage extends EditsSinglePage
{
    protected static string $pageSlug = 'consulting-advisory-services';

    protected static string $pageTemplate = 'solution';

    protected static string $defaultTitleFr = 'Consulting & Advisory';

    protected static ?string $navigationLabel = 'Consulting & Advisory';
    protected static string|\UnitEnum|null $navigationGroup = 'Solutions';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static bool $shouldRegisterNavigation = true;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the Consulting & Advisory page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/consulting-advisory/hero'),
                ]),

            Section::make('Introduction')
                ->description('The lead heading and opening text shown below the hero banner.')
                ->schema([
                    TranslatedField::pairTextarea(
                        'content.introduction.heading',
                        'Lead heading',
                        required: true,
                        rows: 2,
                    ),
                    TranslatedField::pairRichText(
                        'content.introduction.text',
                        'Introduction text',
                        required: true,
                    ),
                ]),

            Section::make('Advisory Services')
                ->description('The service sections shown down the page, with optional images and bullet points.')
                ->schema([
                    Repeater::make('content.sections')
                        ->label('Services')
                        ->schema([
                            TranslatedField::pair('title', 'Service title', required: true),
                            TranslatedField::pairRichText('description', 'Service description', required: true),
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
                                    : 'Bullet point'),
                            FileUpload::make('image')
                                ->label('Service image')
                                ->image()
                                ->directory('pages/consulting-advisory/sections')
                                ->visibility('public'),
                        ])
                        ->addActionLabel('Add advisory service')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['title']['fr'] ?? null)
                            ? str($state['title']['fr'])->limit(60)
                            : 'Advisory service')
                        ->minItems(1),
                ]),
        ];
    }
}
