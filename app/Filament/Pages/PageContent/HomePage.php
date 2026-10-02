<?php

namespace App\Filament\Pages\PageContent;

use App\Filament\Support\TranslatedField;
use App\Models\Page;
use App\Models\PressPost;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Validation\ClosureValidationRule;
use Filament\Support\Icons\Heroicon;
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

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static bool $shouldRegisterNavigation = true;

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
                            Grid::make(2)->schema([
                                TranslatedField::pair('cta_label', 'Button label', required: false),
                                TextInput::make('cta_link')
                                    ->label('Button link')
                                    ->rules([$this->linkRule()])
                                    ->helperText('e.g. /contact'),
                            ]),
                        ])
                        ->addActionLabel('Add slide')
                        ->collapsed()
                        ->itemLabel(fn (array $state): string => filled($state['headline']['fr'] ?? null) ? $state['headline']['fr'] : 'Slide')
                        ->minItems(1),
                    Section::make('Legacy CTA Fallback')
                        ->description('Used only by older frontend implementations when a slide has no CTA.')
                        ->collapsed()
                        ->schema([
                            Grid::make(2)->schema([
                                TranslatedField::pair('content.hero.cta_label', 'Button label', required: false),
                                TextInput::make('content.hero.cta_link')
                                    ->label('Button link')
                                    ->rules([$this->linkRule()]),
                            ]),
                        ]),
                ]),

            Section::make('Featured Spotlights')
                ->description('The featured stories/events shown directly below the hero slider. Add as many as you want to highlight — they display side by side.')
                ->schema([
                    Repeater::make('content.spotlights')
                        ->label('Spotlights')
                        ->schema([
                            TranslatedField::pair('headline', 'Headline'),
                            TranslatedField::pairTextarea('body', 'Spotlight text / link caption', required: false, rows: 2),
                            FileUpload::make('image')
                                ->label('Image')
                                ->image()
                                ->directory('pages/home/spotlights'),
                            TextInput::make('link')
                                ->label('Link')
                                ->rules([$this->linkRule()])
                                ->helperText('URL of the featured story, campaign, or related page — internal path like /press/slug, or a full external https:// URL.'),
                        ])
                        ->addActionLabel('Add spotlight')
                        ->collapsed()
                        ->itemLabel(fn (array $state): string => filled($state['headline']['fr'] ?? null) ? $state['headline']['fr'] : 'Spotlight')
                        ->minItems(0),
                ]),

            Section::make('Digital Banking Feature')
                ->description('The dark video feature section introducing digital banking channels.')
                ->collapsed()
                ->schema([
                    TranslatedField::pair('content.digital_banking.headline', 'Headline', required: false),
                    TranslatedField::pairTextarea('content.digital_banking.body', 'Body text', required: false, rows: 3),
                    FileUpload::make('content.digital_banking.video')
                        ->label('Video file')
                        ->directory('pages/home/digital-banking'),
                    FileUpload::make('content.digital_banking.poster')
                        ->label('Video poster image')
                        ->image()
                        ->directory('pages/home/digital-banking'),
                    FileUpload::make('content.digital_banking.background_image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/home/digital-banking'),
                ]),

            Section::make('Video Section Show')
                ->description('A cinematic slideshow that REPLACES the video player in the section above. Leave the scene list empty to keep showing the video. In a headline, put [square brackets] around one word to highlight it in orange. Scenes play in order — drag to reorder, copy a scene with the duplicate button.')
                ->collapsed()
                ->schema([
                    Select::make('content.digital_banking.show_size')
                        ->label('Frame size')
                        ->options([
                            'medium' => 'Medium (900px wide)',
                            'large' => 'Large (1100px wide)',
                            'full' => 'Full width of the page',
                        ])
                        ->default('large')
                        ->helperText('Everything inside the show (text, card, controls) scales with the frame.'),
                    Toggle::make('content.digital_banking.show_music')
                        ->label('Play background music')
                        ->default(true)
                        ->helperText('Browsers only allow sound after a first click or tap, so music starts then. Visitors can mute it.'),
                    FileUpload::make('content.digital_banking.show_music_file')
                        ->label('Music file (optional)')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/ogg'])
                        ->directory('pages/home/show')
                        ->helperText('MP3, ideally under 3 MB, licensed for commercial web use. Leave empty to use the built-in generated ambient music.'),
                    Repeater::make('content.digital_banking.show_scenes')
                        ->label('Scenes')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Image (optional)')
                                ->image()
                                ->directory('pages/home/show')
                                ->helperText('Landscape, at least 1600px wide, WebP or JPG under 400 KB. Without an image the scene uses the drawn background below.'),
                            FileUpload::make('logo')
                                ->label('Logo badge (optional)')
                                ->image()
                                ->directory('pages/home/show')
                                ->helperText('Shown on a white card on the right of the scene, e.g. a subsidiary or partner logo (transparent PNG, about 1000px wide).'),
                            Grid::make(3)->schema([
                                Select::make('background')
                                    ->label('Drawn background (no image)')
                                    ->options(['net' => 'Digital grid', 'doc' => 'Security pattern', 'code' => 'Code lines'])
                                    ->default('net'),
                                Select::make('focal')
                                    ->label('Keep this part of the image visible')
                                    ->options(['center' => 'Center', 'top' => 'Top', 'bottom' => 'Bottom', 'left' => 'Left', 'right' => 'Right'])
                                    ->default('center'),
                                TextInput::make('duration')
                                    ->label('Seconds on screen')
                                    ->numeric()->minValue(3)->maxValue(20)->default(6.5),
                            ]),
                            TranslatedField::pair('chapter', 'Chapter name (progress bar)'),
                            TranslatedField::pair('headline', 'Headline'),
                            TranslatedField::pairTextarea('subline', 'Sub-line', required: false, rows: 2),
                            Grid::make(3)->schema([
                                Select::make('layout')
                                    ->label('Layout')
                                    ->options(['full' => 'Full image', 'split' => 'Split (text left, image right)'])
                                    ->default('full'),
                                Select::make('align')
                                    ->label('Text position')
                                    ->options(['l' => 'Left', 'c' => 'Center', 'r' => 'Right'])
                                    ->default('l'),
                                Select::make('motion')
                                    ->label('Image movement')
                                    ->options(['zi' => 'Slow zoom in', 'zo' => 'Slow zoom out', 'pl' => 'Pan left', 'pr' => 'Pan right', 'st' => 'Still'])
                                    ->default('zi'),
                                Select::make('transition')
                                    ->label('Transition into this scene')
                                    ->options(['fade' => 'Fade', 'focus' => 'Focus pull (blur to sharp)', 'zoom' => 'Zoom through', 'wipe' => 'Orange wipe'])
                                    ->default('fade'),
                                Select::make('text_animation')
                                    ->label('Headline animation')
                                    ->options(['mask' => 'Rise from a line', 'words' => 'Word by word', 'fade' => 'Fade up', 'slide' => 'Slide in'])
                                    ->default('mask'),
                                Select::make('subline_style')
                                    ->label('Sub-line style')
                                    ->options(['fade' => 'Fade up', 'type' => 'Typewriter'])
                                    ->default('fade'),
                            ]),
                            Grid::make(2)->schema([
                                TranslatedField::pair('cta_label', 'Button label', required: false),
                                TextInput::make('cta_link')
                                    ->label('Button links to')
                                    ->rules([$this->linkRule()])
                                    ->helperText('e.g. /contact'),
                            ]),
                        ])
                        ->addActionLabel('Add scene')
                        ->collapsed()
                        ->cloneable()
                        ->reorderable()
                        ->maxItems(12)
                        ->itemLabel(fn (array $state): string => filled($state['chapter']['fr'] ?? null) ? $state['chapter']['fr'] : 'Scene'),
                ]),

            Section::make('Secondary Banner')
                ->description('Legacy secondary banner content retained for compatibility with existing homepage data.')
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
                            TextInput::make('link')
                                ->label('Links to')
                                ->rules([$this->linkRule()])
                                ->helperText('e.g. /solutions-monetiques'),
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
                        TextInput::make('content.stats_banner.cta_link')
                            ->label('Button link')
                            ->rules([$this->linkRule()]),
                    ]),
                    FileUpload::make('content.stats_banner.background_image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/home/stats'),
                ]),

            Section::make('News Feed')
                ->description('This section pulls automatically from your latest Press Posts — nothing to edit here.')
                ->collapsed()
                ->schema([]),

            Section::make('References Wall')
                ->description('This section pulls automatically from the shared Client References list (see "Client References" in the sidebar) — nothing to edit here.')
                ->collapsed()
                ->schema([]),

            Section::make('Contact CTA')
                ->description('The final contact section with address details and the message form.')
                ->collapsed()
                ->schema([
                    TranslatedField::pair('content.contact.headline', 'Headline', required: false),
                    TranslatedField::pairTextarea('content.contact.body', 'Supporting text', required: false, rows: 3),
                    TextInput::make('content.contact.link')
                        ->label('Contact page link')
                        ->rules([$this->linkRule()])
                        ->default('/contact'),
                    FileUpload::make('content.contact.background_image')
                        ->label('Background image')
                        ->image()
                        ->directory('pages/home/contact'),
                ]),
        ];
    }

    protected function linkRule(): ClosureValidationRule
    {
        return new ClosureValidationRule(function (string $attribute, mixed $value, Closure $fail): void {
            if (blank($value)) {
                return;
            }

            if (filter_var($value, FILTER_VALIDATE_URL)) {
                $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

                if (in_array($scheme, ['http', 'https'], true)) {
                    return;
                }
            }

            if (! is_string($value) || ! str_starts_with($value, '/') || str_starts_with($value, '//')) {
                $fail('Use a valid internal path beginning with / or a complete http(s) URL.');

                return;
            }

            $path = trim((string) parse_url($value, PHP_URL_PATH), '/');
            $segments = $path === '' ? [] : explode('/', $path);

            if (in_array($segments[0] ?? null, ['fr', 'en'], true)) {
                array_shift($segments);
            }

            $slug = $segments[0] ?? '';
            $knownRoute = count($segments) === 1 && in_array($slug, ['', 'contact', 'press'], true);
            $knownPage = $slug !== '' && Page::published()->where('slug', $slug)->exists();
            $knownPage = $knownPage || in_array($slug, [
                'home',
                'nsi-group',
                'references',
                'centre-de-personnalisation-de-kinshasa-2',
                'solutions-monetiques',
                'software-expertise',
                'bank-card',
                'secured-documents',
                'group-joos',
                'kona-i',
                'verve',
                'legal-notices',
                'jobs',
            ], true);
            $knownPressPost = count($segments) === 2
                && $segments[0] === 'press'
                && PressPost::press()->published()->where('slug', $segments[1])->exists();

            if (! $knownRoute && ! $knownPage && ! $knownPressPost) {
                $fail('This internal link does not match a published page or public route.');
            }
        });
    }
}
