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
        $pagesNeedingReview = Page::get()->filter(
            fn (Page $page) => collect($page->content['blocks'] ?? [])->contains('needs_review', true)
        )->count();

        $missingTranslations = TranslationString::whereNull('en')->count();
        $suspectStrings = TranslationString::where('suspect_source_language', true)->count();

        return [
            Stat::make('Pages', $totalPages)
                ->description($pagesNeedingReview > 0 ? "{$pagesNeedingReview} need review" : 'All clear')
                ->color($pagesNeedingReview > 0 ? 'warning' : 'success'),

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