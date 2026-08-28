<?php

namespace App\Filament\Support;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

/**
 * Every translatable leaf in a Page's `content` JSON tree is stored as
 * {"fr": "...", "en": "..."}. These helpers always render the FR and EN
 * inputs side-by-side with explicit language labels — never a single
 * ambiguous field — per the client's own annotation on the old Values
 * block (Value 04 screenshot) flagging exactly that confusion.
 *
 * `$name` is the dot-path prefix WITHOUT the trailing .fr/.en, e.g.
 * 'content.hero.headline' — this produces content.hero.headline.fr and
 * content.hero.headline.en, matching Page::trans()'s expected shape.
 *
 * French is always required (it's the canonical source per TranslationResolver
 * and Page::localeField's fallback logic); English is optional and simply
 * falls back to French at render time when blank.
 */
class TranslatedField
{
    /**
     * Same as pair(), but for use INSIDE a Repeater item where the item
     * itself IS the {fr, en} pair — fields must be named 'fr'/'en' at the
     * item's root, not nested under a prefix key. Producing e.g.
     * content.facts = [{fr: "...", en: "..."}, ...] rather than
     * content.facts = [{"__self": {fr:..., en:...}}, ...].
     */
    public static function pairAtRoot(string $label, bool $required = true): Grid
    {
        return Grid::make(2)
            ->schema([
                TextInput::make('fr')
                    ->label("{$label} (Français)")
                    ->required($required),
                TextInput::make('en')
                    ->label("{$label} (English)")
                    ->helperText('Leave blank to fall back to the French version on the live site.'),
            ]);
    }

    public static function pairTextareaAtRoot(string $label, bool $required = true, int $rows = 3): Grid
    {
        return Grid::make(2)
            ->schema([
                Textarea::make('fr')
                    ->label("{$label} (Français)")
                    ->rows($rows)
                    ->required($required),
                Textarea::make('en')
                    ->label("{$label} (English)")
                    ->rows($rows)
                    ->helperText('Leave blank to fall back to the French version on the live site.'),
            ]);
    }

    public static function pair(string $name, string $label, bool $required = true): Grid
    {
        return Grid::make(2)
            ->schema([
                TextInput::make("{$name}.fr")
                    ->label("{$label} (Français)")
                    ->required($required),
                TextInput::make("{$name}.en")
                    ->label("{$label} (English)")
                    ->helperText('Leave blank to fall back to the French version on the live site.'),
            ]);
    }

    public static function pairTextarea(string $name, string $label, bool $required = true, int $rows = 3): Grid
    {
        return Grid::make(2)
            ->schema([
                Textarea::make("{$name}.fr")
                    ->label("{$label} (Français)")
                    ->rows($rows)
                    ->required($required),
                Textarea::make("{$name}.en")
                    ->label("{$label} (English)")
                    ->rows($rows)
                    ->helperText('Leave blank to fall back to the French version on the live site.'),
            ]);
    }

    public static function pairRichText(string $name, string $label, bool $required = true): Grid
    {
        return Grid::make(2)
            ->schema([
                RichEditor::make("{$name}.fr")
                    ->label("{$label} (Français)")
                    ->required($required)
                    ->columnSpanFull(),
                RichEditor::make("{$name}.en")
                    ->label("{$label} (English)")
                    ->helperText('Leave blank to fall back to the French version on the live site.')
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
