<?php

namespace App\Filament\Support;

use App\Filament\Pages\PageContent\BankCardPage;
use App\Filament\Pages\PageContent\GroupJoosPage;
use App\Filament\Pages\PageContent\KonaIPage;
use App\Filament\Pages\PageContent\SecuredDocumentsPage;
use App\Filament\Pages\PageContent\VervePage;

/**
 * Group JOOS, KONA I, VERVE, Bank Card, and Secured Documents each have a
 * dedicated, hand-built Filament page (GroupJoosPage, KonaIPage, ...) with
 * a content schema matched to their REAL page structure — richer than the
 * generic PartnerResource/ProductResource form.
 *
 * This registry is the single place that says "this slug has a dedicated
 * editor". It's used two ways, both essential to avoiding the data-loss
 * bug this fixes:
 *
 *   1. PartnerResource/ProductResource EXCLUDE these slugs from their own
 *      query — so even navigating directly to /admin/partners/{id}/edit
 *      for a Group JOOS row 404s, rather than opening a form that would
 *      silently wipe its content on save.
 *   2. The sidebar nav links each partner/product to whichever editor is
 *      actually correct for it — the dedicated page if one exists, the
 *      generic resource's edit page otherwise (for any NEW partner/product
 *      added later that doesn't have a hand-built page yet).
 *
 * A slug NEVER appears in both a dedicated page and the generic resource's
 * editable set at the same time — that's the invariant this whole file
 * exists to protect.
 */
class DedicatedPageRegistry
{
    /**
     * @var array<string, class-string>
     */
    public const PARTNERS = [
        'group-joos' => GroupJoosPage::class,
        'kona-i' => KonaIPage::class,
        'verve' => VervePage::class,
    ];

    /**
     * @var array<string, class-string>
     */
    public const PRODUCTS = [
        'bank-card' => BankCardPage::class,
        'secured-documents' => SecuredDocumentsPage::class,
    ];

    public static function partnerSlugs(): array
    {
        return array_keys(self::PARTNERS);
    }

    public static function productSlugs(): array
    {
        return array_keys(self::PRODUCTS);
    }

    public static function dedicatedPartnerPageFor(string $slug): ?string
    {
        return self::PARTNERS[$slug] ?? null;
    }

    public static function dedicatedProductPageFor(string $slug): ?string
    {
        return self::PRODUCTS[$slug] ?? null;
    }
}
