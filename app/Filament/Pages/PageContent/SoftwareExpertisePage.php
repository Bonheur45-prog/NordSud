<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;

class SoftwareExpertisePage extends EditsSinglePage
{
    protected static string $pageSlug = 'software-expertise';

    protected static string $pageTemplate = 'solution';

    protected static string $defaultTitleFr = 'Expertise Logiciel';

    protected static ?string $navigationLabel = 'Software Expertise';
    protected static string|\UnitEnum|null $navigationGroup = 'Solutions';


    protected static bool $shouldRegisterNavigation = true;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the Software Expertise page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/software-expertise/hero'),
                ]),

            Section::make('Introduction')
                ->description('The lead heading and explanatory paragraph shown below the hero banner.')
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

            Section::make('Expertise Points')
                ->description('The checked list of software capabilities shown beside the image.')
                ->schema([
                    Repeater::make('content.points')
                        ->label('Capabilities')
                        ->schema([
                            TranslatedField::pairTextareaAtRoot('Capability', required: true, rows: 2),
                        ])
                        ->addActionLabel('Add capability')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? str($state['fr'])->limit(70)
                            : 'Capability')
                        ->minItems(1),
                ]),

            Section::make('Feature Image')
                ->description('The image displayed to the right of the expertise content.')
                ->schema([
                    FileUpload::make('content.image')
                        ->label('Image')
                        ->image()
                        ->directory('pages/software-expertise')
                        ->visibility('public'),
                ]),
        ];
    }
}
