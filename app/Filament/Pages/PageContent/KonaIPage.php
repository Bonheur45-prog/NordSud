<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;

class KonaIPage extends EditsSinglePage
{
    protected static string $pageSlug = 'kona-i';

    protected static string $pageTemplate = 'partner';

    protected static string $defaultTitleFr = 'KONA I';

    protected static ?string $navigationLabel = 'KONA I';

    protected static bool $shouldRegisterNavigation = false;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the KONA I page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/kona-i/hero')
                        ->visibility('public'),
                ]),

            Section::make('Partner Introduction')
                ->description('The KONA I logo and the partner description shown below the hero banner.')
                ->schema([
                    FileUpload::make('content.introduction.logo')
                        ->label('Partner logo')
                        ->image()
                        ->directory('pages/kona-i')
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
