<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;

class BankCardPage extends EditsSinglePage
{
    protected static string $pageSlug = 'bank-card';

    protected static string $pageTemplate = 'product';

    protected static string $defaultTitleFr = 'Carte Bancaire';

    protected static ?string $navigationLabel = 'Bank Cards';

    protected static bool $shouldRegisterNavigation = false;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the Carte Bancaire page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/bank-card/hero')
                        ->visibility('public'),
                ]),

            Section::make('Introduction')
                ->description('The centered introduction shown beneath the hero banner.')
                ->schema([
                    TranslatedField::pairTextarea(
                        'content.introduction',
                        'Introduction',
                        required: true,
                        rows: 3,
                    ),
                ]),

            Section::make('Product Sections')
                ->description('The three alternating product sections shown down the page.')
                ->schema([
                    Repeater::make('content.sections')
                        ->label('Sections')
                        ->schema([
                            TranslatedField::pair('title', 'Section title', required: true),
                            TranslatedField::pairRichText('description', 'Section description', required: false),
                            Repeater::make('bullets')
                                ->label('Bullet points')
                                ->schema([
                                    TranslatedField::pairTextareaAtRoot('Bullet point', required: true, rows: 2),
                                ])
                                ->addActionLabel('Add bullet point')
                                ->collapsible()
                                ->reorderable()
                                ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                                    ? str($state['fr'])->limit(70)
                                    : 'Bullet point'),
                            FileUpload::make('image')
                                ->label('Section image')
                                ->image()
                                ->directory('pages/bank-card/sections')
                                ->visibility('public'),
                        ])
                        ->addActionLabel('Add product section')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['title']['fr'] ?? null)
                            ? str($state['title']['fr'])->limit(70)
                            : 'Product section')
                        ->minItems(1),
                ]),
        ];
    }
}
