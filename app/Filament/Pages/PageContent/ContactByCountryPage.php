<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;

class ContactByCountryPage extends EditsSinglePage
{
    protected static string $pageSlug = 'contact';

    protected static string $pageTemplate = 'contact';

    protected static string $defaultTitleFr = 'Contact par pays';

    protected static ?string $navigationLabel = 'Contact par pays';

    protected static bool $shouldRegisterNavigation = false;

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Banner')
                ->description('The map banner shown at the top of the Contact page.')
                ->schema([
                    FileUpload::make('content.hero.image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/contact/hero')
                        ->visibility('public'),
                ]),

            Section::make('Addresses by Country')
                ->description('The country-by-country addresses shown near the top of the page.')
                ->schema([
                    Repeater::make('content.addresses')
                        ->label('Addresses')
                        ->schema([
                            TranslatedField::pair('country', 'Country', required: true),
                            TranslatedField::pairTextarea('address', 'Address', required: true, rows: 3),
                        ])
                        ->addActionLabel('Add country address')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['country']['fr'] ?? null)
                            ? str($state['country']['fr'])->limit(50)
                            : 'Country address')
                        ->minItems(1),
                ]),

            Section::make('Telephone by Country')
                ->description('The country-specific telephone numbers and opening hours.')
                ->schema([
                    Repeater::make('content.telephones')
                        ->label('Telephone entries')
                        ->schema([
                            TranslatedField::pair('country', 'Country', required: true),
                            TranslatedField::pairTextarea('details', 'Telephone and hours', required: true, rows: 3),
                        ])
                        ->addActionLabel('Add telephone entry')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['country']['fr'] ?? null)
                            ? str($state['country']['fr'])->limit(50)
                            : 'Telephone entry')
                        ->minItems(1),
                ]),

            Section::make('Contact Form')
                ->description('The message above the contact form and the labels used by its fields.')
                ->schema([
                    TranslatedField::pairTextarea(
                        'content.form.introduction',
                        'Form introduction',
                        required: true,
                        rows: 3,
                    ),
                    Repeater::make('content.form.fields')
                        ->label('Form fields')
                        ->schema([
                            TextInput::make('key')
                                ->label('Field key')
                                ->required()
                                ->helperText('Use a stable key such as company, first_name, email, or message.'),
                            TranslatedField::pair('label', 'Field label', required: true),
                            Textarea::make('placeholder_fr')
                                ->label('Placeholder (Français)')
                                ->rows(2),
                            Textarea::make('placeholder_en')
                                ->label('Placeholder (English)')
                                ->rows(2),
                        ])
                        ->addActionLabel('Add form field')
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(fn (array $state): string => filled($state['label']['fr'] ?? null)
                            ? str($state['label']['fr'])->limit(60)
                            : 'Form field')
                        ->minItems(1),
                ]),
        ];
    }
}
