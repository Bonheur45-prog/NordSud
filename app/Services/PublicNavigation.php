<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Collection;

/**
 * Single source of truth for the public site's navigation structure —
 * used by BOTH the header dropdowns and the footer columns, so they can
 * never drift out of sync with each other (unlike the old WordPress site,
 * where the nav menu and footer links were maintained completely
 * separately and regularly disagreed).
 *
 * Deliberately does NOT know about DedicatedPageRegistry (the admin-side
 * distinction between hand-built and generic editors) — that's purely an
 * editing-conflict concern. A visitor only cares that a page is published
 * and belongs to a given template group.
 */
class PublicNavigation
{
    /**
     * @return Collection<int, Page>
     */
    public function pagesForTemplate(string $template): Collection
    {
        return Page::published()
            ->where('template', $template)
            ->orderBy('order')
            ->get();
    }

    /**
     * The full header nav structure: fixed top-level items plus dropdown
     * groups pulled live from published pages.
     *
     * @return array<int, array{label: string, url: string|null, children: Collection<int, Page>|null}>
     */
    public function mainGroups(): array
    {
        return [
            [
                'key' => 'about',
                'label_key' => 'nav.about',
                'children' => $this->pagesForTemplate('about')
                    ->merge($this->pagesForTemplate('references')),
            ],
            [
                'key' => 'solutions',
                'label_key' => 'nav.solutions',
                'children' => $this->pagesForTemplate('solution'),
            ],
            [
                'key' => 'products',
                'label_key' => 'nav.products',
                'children' => $this->pagesForTemplate('product'),
            ],
            [
                'key' => 'partners',
                'label_key' => 'nav.partners',
                'children' => $this->pagesForTemplate('partner'),
            ],
        ];
    }

    /**
     * Footer "Company" column — About-type pages plus Press/Jobs, which
     * aren't full page templates but still belong in this grouping.
     */
    public function footerCompanyLinks(): Collection
    {
        return $this->pagesForTemplate('about')
            ->merge($this->pagesForTemplate('references'));
    }

    /**
     * Footer "Solutions" column — Solutions, Products, and Partners
     * together, matching the footer's single combined column.
     */
    public function footerSolutionLinks(): Collection
    {
        return $this->pagesForTemplate('solution')
            ->merge($this->pagesForTemplate('product'))
            ->merge($this->pagesForTemplate('partner'));
    }
}
