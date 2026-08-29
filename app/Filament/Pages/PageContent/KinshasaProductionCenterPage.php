<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;

class KinshasaProductionCenterPage extends EditsSinglePage
{
    protected static string $pageSlug = 'centre-de-personnalisation-de-kinshasa-2';

    protected static string $pageTemplate = 'about';

    protected static string $defaultTitleFr = 'Centre de Production Kinshasa';

    protected static ?string $navigationLabel = 'Kinshasa Production Center';
    protected static string|\UnitEnum|null $navigationGroup = 'About';


    protected static bool $shouldRegisterNavigation = true;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the Kinshasa Production Center page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/kinshasa-production-center/hero'),
                ]),

            Section::make('Opening')
                ->description('The first image and introductory paragraphs below the hero banner.')
                ->schema([
                    FileUpload::make('content.opening.image')
                        ->label('Opening image')
                        ->image()
                        ->directory('pages/kinshasa-production-center'),
                    TranslatedField::pairRichText('content.opening.text', 'Opening text', required: true),
                ]),

            Section::make('Facility')
                ->description('The production center exterior image and its accompanying text.')
                ->schema([
                    FileUpload::make('content.facility.image')
                        ->label('Facility image')
                        ->image()
                        ->directory('pages/kinshasa-production-center'),
                    TranslatedField::pairRichText('content.facility.text', 'Facility text', required: true),
                ]),

            Section::make('Certifications')
                ->description('The interior image and certification or compliance information.')
                ->schema([
                    FileUpload::make('content.certifications.image')
                        ->label('Interior image')
                        ->image()
                        ->directory('pages/kinshasa-production-center'),
                    TranslatedField::pairRichText('content.certifications.text', 'Certification text', required: true),
                ]),

            Section::make('Closing')
                ->description('The final paragraph shown at the bottom of the page.')
                ->schema([
                    TranslatedField::pairRichText('content.closing.text', 'Closing text', required: true),
                ]),
        ];
    }
}
