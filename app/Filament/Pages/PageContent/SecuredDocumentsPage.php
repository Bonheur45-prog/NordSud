<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;

class SecuredDocumentsPage extends EditsSinglePage
{
    protected static string $pageSlug = 'secured-documents';

    protected static string $pageTemplate = 'product';

    protected static string $defaultTitleFr = 'Documents Sécurisés';

    protected static ?string $navigationLabel = 'Secured Documents';

    protected static bool $shouldRegisterNavigation = false;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The wide banner at the top of the Documents Sécurisés page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/secured-documents/hero')
                        ->visibility('public'),
                ]),

            Section::make('Introduction')
                ->description('The centered lead statement shown beneath the hero banner.')
                ->schema([
                    TranslatedField::pairTextarea(
                        'content.introduction',
                        'Introduction',
                        required: true,
                        rows: 4,
                    ),
                ]),

            Section::make('Security Mechanisms')
                ->description('The first content band: explanatory text, security bullets, and the image on the right.')
                ->schema([
                    TranslatedField::pairRichText('content.security.description', 'Description', required: true),
                    Repeater::make('content.security.bullets')
                        ->label('Security mechanisms')
                        ->schema([
                            TranslatedField::pairTextareaAtRoot('Security mechanism', required: true, rows: 2),
                        ])
                        ->addActionLabel('Add security mechanism')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? str($state['fr'])->limit(70)
                            : 'Security mechanism')
                        ->minItems(1),
                    FileUpload::make('content.security.image')
                        ->label('Section image')
                        ->image()
                        ->directory('pages/secured-documents/security')
                        ->visibility('public'),
                ]),

            Section::make('Integrated Services')
                ->description('The second content band: image on the left, service description, and service bullets.')
                ->schema([
                    TranslatedField::pairRichText('content.services.description', 'Description', required: true),
                    Repeater::make('content.services.bullets')
                        ->label('Integrated services')
                        ->schema([
                            TranslatedField::pairTextareaAtRoot('Service', required: true, rows: 2),
                        ])
                        ->addActionLabel('Add integrated service')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['fr'] ?? null)
                            ? str($state['fr'])->limit(70)
                            : 'Integrated service')
                        ->minItems(1),
                    FileUpload::make('content.services.image')
                        ->label('Section image')
                        ->image()
                        ->directory('pages/secured-documents/services')
                        ->visibility('public'),
                ]),

            Section::make('Closing Statement')
                ->description('The final paragraph below the integrated services section.')
                ->schema([
                    TranslatedField::pairRichText('content.closing', 'Closing statement', required: true),
                ]),
        ];
    }
}
