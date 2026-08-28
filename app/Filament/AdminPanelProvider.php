<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\Products\ProductResource;
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
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // 1. Build the dynamic navigation items from the database
        $partnerItems = Page::where('template', 'partner')
            ->orderBy('order')
            ->get()
            ->map(fn (Page $partner) => NavigationItem::make($partner->title_fr ?? $partner->slug)
                ->url(fn () => PartnerResource::getUrl('edit', ['record' => $partner]))
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->group('Partners')
                ->sort(10))
            ->all();

        $partnerItems[] = NavigationItem::make('Add New Partner')
            ->url(fn () => PartnerResource::getUrl('create'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->group('Partners')
            ->sort(99);

        $productItems = Page::where('template', 'product')
            ->orderBy('order')
            ->get()
            ->map(fn (Page $product) => NavigationItem::make($product->title_fr ?? $product->slug)
                ->url(fn () => ProductResource::getUrl('edit', ['record' => $product]))
                ->icon(Heroicon::OutlinedCreditCard)
                ->group('Products')
                ->sort(10))
            ->all();

        $productItems[] = NavigationItem::make('Add New Product')
            ->url(fn () => ProductResource::getUrl('create'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->group('Products')
            ->sort(99);

        // 2. Return the fluent panel configuration chain
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\CmsOverviewWidget::class,
            ])
            ->navigationItems([
                ...$partnerItems,
                ...$productItems,
            ])
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
}
