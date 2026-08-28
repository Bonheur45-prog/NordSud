<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use UnitEnum;

/**
 * The Home page — the richest page on the site. Matches, top-to-bottom,
 * what the live nsi-monetique.com home page actually shows: hero slider,
 * featured press callout, secondary banner with embedded video, a 6-item
 * icon grid linking to other sections, a stats/CTA dark banner, the
 * references logo wall (pulled from the shared Client References list —
 * see ClientReferenceResource — not re-entered here), and a news feed
 * (pulled live from Press Posts, not manually entered here either).
 */
class HomePage extends EditsSinglePage
{
    protected static string $pageSlug = 'home';

    protected static string $pageTemplate = 'home';

    protected static string $defaultTitleFr = 'Accueil';

    protected static ?string $navigationLabel = 'Home Page';

    protected static bool $shouldRegisterNavigation = false;

    protected static string|UnitEnum|null $navigationGroup = null; // top-level, not nested

    protected static ?int $navigationSort = -10; // always first in the sidebar

    protected function contentSchema(): array
    {
        return [
            Section::make('Hero Slider')
                ->description('The rotating banner slides at the very top of the home page.')
                ->schema([
                    Repeater::make('content.hero.slides')
                        ->label('Slides')
                        ->schema([
                            TranslatedField::pair('eyebrow', 'Eyebrow (small text above headline)', required: false),
                            TranslatedField::pair('headline', 'Headline'),
                            TranslatedField::pairTextarea('subline', 'Subtitle', required: false, rows: 2),
                            FileUpload::make('image')
                                ->label('Background image')
                                ->image()
                                ->directory('pages/home/hero'),
                        ])
                        ->addActionLabel('Add slide')
                        ->collapsed()
                        ->itemLabel(fn (array $state): string => filled($state['headline']['fr'] ?? null) ? $state['headline']['fr'] : 'Slide')
                        ->minItems(1),
                    Grid::make(2)->schema([
                        TranslatedField::pair('content.hero.cta_label', 'Button label', required: false),
                        TextInput::make('content.hero.cta_link')
                            ->label('Button link')
                            ->helperText('e.g. /contact'),
                    ]),
                ]),

            Section::make('Press Spotlight')
                ->description('The single featured news item shown just under the hero slider.')
                ->collapsed()
                ->schema([
                    TranslatedField::pair('content.press_spotlight.headline', 'Headline', required: false),
                    FileUpload::make('content.press_spotlight.image')
                        ->label('Image')
                        ->image()
                        ->directory('pages/home'),
                    TextInput::make('content.press_spotlight.link')
                        ->label('Link')
                        ->helperText('URL of the press article this links to.'),
                ]),

            Section::make('Secondary Banner')
                ->description('The dark full-width banner with an embedded video.')
                ->collapsed()
                ->schema([
                    TranslatedField::pair('content.secondary_banner.headline', 'Headline', required: false),
                    TranslatedField::pairTextarea('content.secondary_banner.body', 'Body text', required: false, rows: 2),
                    FileUpload::make('content.secondary_banner.video')
                        ->label('Video file')
                        ->directory('pages/home')
                        ->helperText('Upload the video shown in this banner.'),
                    FileUpload::make('content.secondary_banner.background_image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/home'),
                ]),

            Section::make('Section Highlights')
                ->description('The grid of 6 cards linking to other sections (Solutions, Cartes Bancaires, Documents Sécurisés, etc).')
                ->schema([
                    Repeater::make('content.highlights')
                        ->label('Cards')
                        ->schema([
                            TranslatedField::pair('title', 'Title'),
                            TranslatedField::pairTextarea('description', 'Description', required: false, rows: 2),
                            FileUpload::make('image')->label('Image')->image()->directory('pages/home/highlights'),
                            TextInput::make('link')->label('Links to')->helperText('e.g. /solutions-monetiques'),
                        ])
                        ->addActionLabel('Add card')
                        ->collapsed()
                        ->itemLabel(fn (array $state): string => filled($state['title']['fr'] ?? null) ? $state['title']['fr'] : 'Card')
                        ->minItems(1)
                        ->maxItems(6),
                ]),

            Section::make('Stats / CTA Banner')
                ->description('The dark banner with the "60+ banks trust us" style statement and a contact button.')
                ->collapsed()
                ->schema([
                    TranslatedField::pairTextarea('content.stats_banner.headline', 'Headline', required: false, rows: 2),
                    Grid::make(2)->schema([
                        TranslatedField::pair('content.stats_banner.cta_label', 'Button label', required: false),
                        TextInput::make('content.stats_banner.cta_link')->label('Button link'),
                    ]),
                ]),

            Section::make('News Feed')
                ->description('This section pulls automatically from your latest Press Posts — nothing to edit here.')
                ->collapsed()
                ->schema([]),

            Section::make('References Wall')
                ->description('This section pulls automatically from the shared Client References list (see "Client References" in the sidebar) — nothing to edit here.')
                ->collapsed()
                ->schema([]),
        ];
    }
}
