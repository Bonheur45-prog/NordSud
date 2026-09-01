<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Support\DedicatedPageRegistry;
use App\Models\Page;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Cookie\Middleware\EncryptCookies;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\CmsOverviewWidget::class,
            ])
            ->navigationItems($this->buildDynamicNavigationItems())
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Wrapped in try/catch: this runs every time the panel boots, which
     * includes contexts before migrations have run yet (a fresh clone,
     * `composer install`, CI, or the app-boot phase in some test setups).
     * Only the "table doesn't exist yet" case is swallowed — narrowed to
     * QueryException specifically, so any other failure surfaces loudly
     * instead of silently producing an empty sidebar.
     *
     * Public (not protected) so this can be tested directly without
     * depending on full HTTP-request boot-order timing.
     */
    public function buildDynamicNavigationItems(): array
    {
        try {
            $partnerItems = $this->itemsFor(
                template: 'partner',
                group: 'Partners',
                dedicatedLookup: fn (string $slug) => DedicatedPageRegistry::dedicatedPartnerPageFor($slug),
                genericEditUrl: fn (Page $page) => PartnerResource::getUrl('edit', ['record' => $page]),
                genericCreateUrl: fn () => PartnerResource::getUrl('create'),
                addLabel: 'Add New Partner',
                icon: Heroicon::OutlinedBuildingOffice2,
            );

            $productItems = $this->itemsFor(
                template: 'product',
                group: 'Products',
                dedicatedLookup: fn (string $slug) => DedicatedPageRegistry::dedicatedProductPageFor($slug),
                genericEditUrl: fn (Page $page) => ProductResource::getUrl('edit', ['record' => $page]),
                genericCreateUrl: fn () => ProductResource::getUrl('create'),
                addLabel: 'Add New Product',
                icon: Heroicon::OutlinedCreditCard,
            );
        } catch (\Illuminate\Database\QueryException) {
            return [];
        }

        return [...$partnerItems, ...$productItems];
    }

    /**
     * @param  \Closure(string): ?string  $dedicatedLookup
     * @param  \Closure(Page): string  $genericEditUrl
     * @param  \Closure(): string  $genericCreateUrl
     * @return array<NavigationItem>
     */
    protected function itemsFor(
        string $template,
        string $group,
        \Closure $dedicatedLookup,
        \Closure $genericEditUrl,
        \Closure $genericCreateUrl,
        string $addLabel,
        Heroicon $icon,
    ): array {
        $items = Page::where('template', $template)
            ->orderBy('order')
            ->get()
            ->map(function (Page $page) use ($dedicatedLookup, $genericEditUrl, $group, $icon) {
                $dedicatedPageClass = $dedicatedLookup($page->slug);

                $url = $dedicatedPageClass
                    ? fn () => $dedicatedPageClass::getUrl()
                    : fn () => $genericEditUrl($page);

                return NavigationItem::make($page->title_fr ?? $page->slug)
                    ->url($url)
                    ->icon($icon)
                    ->group($group)
                    ->sort(10);
            })
            ->all();

        $items[] = NavigationItem::make($addLabel)
            ->url($genericCreateUrl)
            ->icon(Heroicon::OutlinedPlusCircle)
            ->group($group)
            ->sort(99);

        return $items;
    }
}
