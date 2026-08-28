<?php

namespace App\Filament\Pages\PageContent;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Every concrete page (HomePage, NsiGroupPage, ContactPage, ...) extends
 * this. It is NOT a page builder: the slug and template are fixed at the
 * class level, the record is found-or-created automatically, and the form
 * shows the page's entire real content top-to-bottom in one place — the
 * editor never chooses a template or creates new "Page" records.
 *
 * A subclass only needs to set $pageSlug / $pageTemplate / $defaultTitleFr
 * and implement contentSchema(). No custom Blade view is needed — this
 * uses Filament's own default page view + content() schema, the same
 * mechanism Filament's built-in EditRecord page uses.
 */
abstract class EditsSinglePage extends FilamentPage
{
    /**
     * The fixed `pages.slug` this editor manages. Never chosen by the user.
     */
    protected static string $pageSlug;

    /**
     * The fixed `pages.template` value (decides which public Blade view
     * renders this page) — set once here, never exposed as a form field.
     */
    protected static string $pageTemplate;

    /**
     * Used only the first time this page is opened and no row exists yet.
     */
    protected static string $defaultTitleFr = 'Untitled';

    public ?array $data = [];

    public ?int $recordId = null;

    public function mount(): void
    {
        $record = Page::firstOrCreate(
            ['slug' => static::$pageSlug],
            [
                'template' => static::$pageTemplate,
                'status' => 'draft',
                'order' => 0,
                'title_fr' => static::$defaultTitleFr,
            ]
        );

        $this->recordId = $record->id;

        $this->form->fill($record->attributesToArray());
    }

    /**
     * Re-fetches the record on every call rather than caching the model
     * instance in a property — Livewire only persists PUBLIC properties
     * across request/update cycles and does not re-run mount() on
     * subsequent calls, so a cached model on a protected property would
     * be uninitialized by the time an action like save() runs.
     */
    protected function getRecord(): Page
    {
        return Page::findOrFail($this->recordId);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Title & SEO')
                    ->description('The page title and search engine preview text. Not shown as a visible heading unless the page template says so.')
                    ->collapsed()
                    ->schema($this->titleAndSeoFields()),
                ...$this->contentSchema(),
            ])
            ->statePath('data');
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    protected function titleAndSeoFields(): array
    {
        // NOTE: these use flat field names (title_fr, not title.fr) because
        // Page's title/meta columns are flat DB columns, unlike the nested
        // {fr, en} pairs used inside `content`. TranslatedField::pair()
        // assumes nested naming and would silently fail to save here.
        return [
            Grid::make(2)->schema([
                TextInput::make('title_fr')->label('Page title (Français)')->required(),
                TextInput::make('title_en')->label('Page title (English)')
                    ->helperText('Leave blank to fall back to the French version on the live site.'),
            ]),
            Grid::make(2)->schema([
                TextInput::make('meta_title_fr')->label('SEO meta title (Français)'),
                TextInput::make('meta_title_en')->label('SEO meta title (English)'),
            ]),
            Grid::make(2)->schema([
                Textarea::make('meta_description_fr')->label('SEO meta description (Français)')->rows(2),
                Textarea::make('meta_description_en')->label('SEO meta description (English)')->rows(2),
            ]),
        ];
    }

    /**
     * Subclasses define the page's real content blocks here (Hero, Story,
     * Values, Stats, whatever the actual page has) — matching top-to-bottom
     * what the live page shows, per the "content editor, not page builder"
     * requirement.
     *
     * @return array<\Filament\Schemas\Components\Component>
     */
    abstract protected function contentSchema(): array;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Action::make('save')
                            ->label('Save')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->getRecord()->fill($data)->save();

        Notification::make()
            ->title('Saved')
            ->success()
            ->send();
    }
}
