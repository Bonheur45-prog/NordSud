<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\PressPost;
use App\Models\TranslationString;
use App\Support\MissingTranslations;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CmsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPages = Page::count();

        // NOTE: there used to be a "pages needing review" stat here that
        // checked content['blocks'] for a needs_review flag. That was a
        // leftover from the generic block-editor architecture. Every page
        // now has its own bespoke content shape (Home uses hero/stats/
        // highlights, Partner uses logo/paragraphs, etc.) with no shared
        // "blocks" key — so that check silently always returned 0. Removed
        // rather than left in as a permanently-wrong "All clear" badge.
        // If a real per-template "needs attention" signal is wanted later,
        // it should be defined per dedicated page, not generically here.

        // Two sources of missing English: the global UI strings table, and
        // every {fr, en} pair / *_en column in pages, press and settings
        // (see App\Support\MissingTranslations). Counting only the first
        // made the card show 0 while page content still fell back to French.
        $missingStrings = TranslationString::where(fn ($q) => $q->whereNull('en')->orWhere('en', ''))->count();
        $missingContent = MissingTranslations::count();
        $missingTranslations = $missingStrings + $missingContent;
        $suspectStrings = TranslationString::where('suspect_source_language', true)->count();

        return [
            Stat::make('Pages', $totalPages),

            Stat::make('Missing English translations', $missingTranslations)
                ->description($missingTranslations > 0
                    ? "{$missingContent} in pages & press · {$missingStrings} in strings"
                    : 'All translated')
                ->color($missingTranslations > 0 ? 'danger' : 'success'),

            Stat::make('Suspect source language', $suspectStrings)
                ->description($suspectStrings > 0
                    ? 'Flagged during migration — verify the French value'
                    : 'None flagged')
                ->color($suspectStrings > 0 ? 'warning' : 'success'),

            Stat::make('Press & job posts', PressPost::count())
                ->description(PressPost::where('status', 'draft')->count().' in draft'),
        ];
    }
}