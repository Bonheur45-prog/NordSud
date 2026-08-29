<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\PressPost;
use App\Models\TranslationString;
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

        $missingTranslations = TranslationString::whereNull('en')->count();
        $suspectStrings = TranslationString::where('suspect_source_language', true)->count();

        return [
            Stat::make('Pages', $totalPages),

            Stat::make('Missing English translations', $missingTranslations)
                ->description($missingTranslations > 0 ? 'Needs translating' : 'All translated')
                ->color($missingTranslations > 0 ? 'danger' : 'success'),

            Stat::make('Suspect source language', $suspectStrings)
                ->description('Flagged during migration — verify the French value')
                ->color($suspectStrings > 0 ? 'warning' : 'success'),

            Stat::make('Press & job posts', PressPost::count())
                ->description(PressPost::where('status', 'draft')->count().' in draft'),
        ];
    }
}