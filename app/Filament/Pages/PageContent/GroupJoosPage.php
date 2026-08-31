<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class GroupJoosPage extends EditsSinglePage
{
    protected static string $pageSlug = 'group-joos';

    protected static string $pageTemplate = 'partner';

    protected static string $defaultTitleFr = 'Group JOOS';

    protected static ?string $navigationLabel = 'Group JOOS';

    protected static bool $shouldRegisterNavigation = false;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the Group JOOS page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/group-joos/hero')
                        ->visibility('public'),
                ]),

            Section::make('Partner Introduction')
                ->description('The partner logo and introduction shown below the hero banner.')
                ->schema([
                    FileUpload::make('content.introduction.logo')
                        ->label('Partner logo')
                        ->image()
                        ->directory('pages/group-joos')
                        ->visibility('public'),
                    TranslatedField::pairRichText('content.introduction.text', 'Introduction', required: true),
                ]),

            Section::make('Key Facts')
                ->description('The partner facts list displayed beside the first facility image.')
                ->schema([
                    Repeater::make('content.facts')
                        ->label('Facts')
                        ->schema([
                            TranslatedField::pairTextareaAtRoot('Fact', required: true, rows: 2),
                        ])
                        ->addActionLabel('Add fact')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? str($state['fr'])->limit(70)
                            : 'Fact')
                        ->minItems(1),
                    FileUpload::make('content.facility.image')
                        ->label('Facility image')
                        ->image()
                        ->directory('pages/group-joos/facility')
                        ->visibility('public'),
                ]),

            Section::make('Guarantees & Certificates')
                ->description('The certification heading, list, and certificate or certification image.')
                ->schema([
                    TranslatedField::pair('content.certificates.title', 'Section title', required: true),
                    Repeater::make('content.certificates.items')
                        ->label('Certificates')
                        ->schema([
                            Toggle::make('heading')
                                ->label('Category heading')
                                ->helperText('Turn on for a category label like "QUALITÉ:" or "ENVIRONNEMENT:" — shown without a checkmark, above the items that follow it. Leave off for a normal checked certificate/standard.')
                                ->default(false),
                            TranslatedField::pairTextareaAtRoot('Certificate / heading text', required: true, rows: 2),
                        ])
                        ->addActionLabel('Add certificate or heading')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? (($state['heading'] ?? false) ? '— '.str($state['fr'])->limit(60).' (heading)' : str($state['fr'])->limit(70))
                            : 'Certificate')
                        ->minItems(1),
                    FileUpload::make('content.certificates.image')
                        ->label('Certificate image')
                        ->image()
                        ->directory('pages/group-joos/certificates')
                        ->visibility('public'),
                ]),

            Section::make('Production Sites')
                ->description('The three production-site images and their captions.')
                ->schema([
                    TranslatedField::pair('content.production_sites.title', 'Section title', required: true),
                    Repeater::make('content.production_sites.items')
                        ->label('Sites')
                        ->schema([
                            TranslatedField::pair('name', 'Site name', required: true),
                            FileUpload::make('image')
                                ->label('Site image')
                                ->image()
                                ->directory('pages/group-joos/production-sites')
                                ->visibility('public'),
                        ])
                        ->addActionLabel('Add production site')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['name']['fr'] ?? null)
                            ? str($state['name']['fr'])->limit(70)
                            : 'Production site')
                        ->minItems(1),
                ]),

            Section::make('Production Capacity')
                ->description('The capacity heading, bullet list, and production-floor gallery.')
                ->schema([
                    TranslatedField::pair('content.capacity.title', 'Section title', required: true),
                    Repeater::make('content.capacity.points')
                        ->label('Capacity points')
                        ->schema([
                            TranslatedField::pairTextareaAtRoot('Capacity point', required: true, rows: 2),
                        ])
                        ->addActionLabel('Add capacity point')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? str($state['fr'])->limit(70)
                            : 'Capacity point')
                        ->minItems(1),
                    Repeater::make('content.capacity.images')
                        ->label('Production images')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Image')
                                ->image()
                                ->directory('pages/group-joos/production')
                                ->visibility('public'),
                        ])
                        ->addActionLabel('Add production image')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (): string => 'Production image'),
                ]),

            Section::make('Secure Document Certification')
                ->description('The final certification section and certificate image.')
                ->schema([
                    TranslatedField::pair('content.secure_document_certification.title', 'Section title', required: true),
                    TranslatedField::pairRichText('content.secure_document_certification.text', 'Certification text', required: true),
                    FileUpload::make('content.secure_document_certification.image')
                        ->label('Certification image')
                        ->image()
                        ->directory('pages/group-joos/secure-document-certification')
                        ->visibility('public'),
                ]),
        ];
    }
}
