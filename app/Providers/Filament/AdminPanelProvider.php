<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Pages\PageContent\NsiGroupPage;
use App\Models\Page;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $homeItem = NavigationItem::make('Home Page')
            ->url(fn () => \App\Filament\Pages\PageContent\HomePage::getUrl())
            ->icon(Heroicon::OutlinedHome)
            ->sort(-100);

        $aboutItems = Page::whereIn('template', ['about', 'references'])
            ->where('slug', '!=', 'nsi-group')
            ->orderBy('order')
            ->get()
            ->map(fn (Page $about) => NavigationItem::make($about->title_fr ?? $about->slug)
                ->url(fn () => route('page.show', ['locale' => 'fr', 'slug' => $about->slug]))
                ->icon(Heroicon::OutlinedInformationCircle)
                ->group('About')
                ->sort(10))
            ->all();

        array_unshift($aboutItems, NavigationItem::make('NSI Group')
            ->url(fn () => NsiGroupPage::getUrl())
            ->icon(Heroicon::OutlinedInformationCircle)
            ->group('About')
            ->sort(10));

        $aboutItems[] = NavigationItem::make('Kinshasa Production Center')
            ->url(fn () => route('page.show', ['locale' => 'fr', 'slug' => 'centre-de-personnalisation-de-kinshasa-2']))
            ->icon(Heroicon::OutlinedBuildingOffice2)
            ->group('About')
            ->sort(12);

        $solutionItems = Page::where('template', 'solution')
            ->orderBy('order')
            ->get()
            ->map(fn (Page $solution) => NavigationItem::make($solution->title_fr ?? $solution->slug)
                ->url(fn () => route('page.show', ['locale' => 'fr', 'slug' => $solution->slug]))
                ->icon(Heroicon::OutlinedLightBulb)
                ->group('Solutions')
                ->sort(30))
            ->all();

        $partnerItems = Page::where('template', 'partner')
            ->orderBy('order')
            ->get()
            ->map(fn (Page $partner) => NavigationItem::make($partner->title_fr ?? $partner->slug)
                ->url(fn () => PartnerResource::getUrl('edit', ['record' => $partner]))
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->group('Partners')
                ->sort(50))
            ->all();

        $partnerItems[] = NavigationItem::make('Add New Partner')
            ->url(fn () => PartnerResource::getUrl('create'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->group('Partners')
            ->sort(59);

        $productItems = Page::where('template', 'product')
            ->orderBy('order')
            ->get()
            ->map(fn (Page $product) => NavigationItem::make($product->title_fr ?? $product->slug)
                ->url(fn () => ProductResource::getUrl('edit', ['record' => $product]))
                ->icon(Heroicon::OutlinedCreditCard)
                ->group('Products')
                ->sort(40))
            ->all();

        $productItems[] = NavigationItem::make('Add New Product')
            ->url(fn () => ProductResource::getUrl('create'))
            ->icon(Heroicon::OutlinedPlusCircle)
            ->group('Products')
            ->sort(49);

        $contactItem = NavigationItem::make('Contact Page')
            ->url(fn () => route('contact.show', ['locale' => 'fr']))
            ->icon(Heroicon::OutlinedEnvelope)
            ->sort(60);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->navigationItems([
                $homeItem,
                ...$aboutItems,
                ...$solutionItems,
                ...$productItems,
                ...$partnerItems,
                $contactItem,
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
