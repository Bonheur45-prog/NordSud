<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;

class VervePage extends EditsSinglePage
{
    protected static string $pageSlug = 'verve';

    protected static string $pageTemplate = 'partner';

    protected static string $defaultTitleFr = 'VERVE';

    protected static ?string $navigationLabel = 'VERVE';

    protected static bool $shouldRegisterNavigation = false;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the VERVE page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/verve/hero')
                        ->visibility('public'),
                ]),

            Section::make('Partner Introduction')
                ->description('The VERVE logo and partner description shown below the hero banner.')
                ->schema([
                    FileUpload::make('content.introduction.logo')
                        ->label('Partner logo')
                        ->image()
                        ->directory('pages/verve')
                        ->visibility('public'),
                    Repeater::make('content.paragraphs')
                        ->label('Introduction paragraphs')
                        ->schema([
                            TranslatedField::pairTextareaAtRoot('Paragraph', required: true, rows: 5),
                        ])
                        ->addActionLabel('Add paragraph')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? str($state['fr'])->limit(70)
                            : 'Paragraph')
                        ->minItems(1),
                ]),
        ];
    }
}
