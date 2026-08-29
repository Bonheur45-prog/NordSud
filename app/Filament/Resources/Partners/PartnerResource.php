<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Schemas\PartnerForm;
use App\Filament\Resources\Partners\Tables\PartnersTable;
use App\Filament\Support\DedicatedPageRegistry;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    // Page is the canonical store for all partner pages. Direct partner links
    // are rendered through custom NavigationItems in AdminPanelProvider.
    protected static bool $shouldRegisterNavigation = false;

    /**
     * Group JOOS, KONA I, and VERVE each have a dedicated, hand-built page
     * editor (see App\Filament\Pages\PageContent\*) with a content schema
     * matched to their real, richer structure. This resource's generic
     * form uses a different, simpler shape — so those slugs are EXCLUDED
     * here. Without this, opening one of them through this generic editor
     * and hitting Save would silently overwrite its real content with the
     * generic shape. This resource now only ever touches partners that
     * DON'T have a dedicated page yet (i.e. new ones added after launch).
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('template', 'partner')
            ->whereNotIn('slug', DedicatedPageRegistry::partnerSlugs());
    }

    public static function form(Schema $schema): Schema
    {
        return PartnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnersTable::configure($table);
    }

    /**
     * No "index" list page — every partner (dedicated or generic) appears
     * as its own direct link in the sidebar via AdminPanelProvider, not
     * through an intermediate list page.
     */
    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return \Filament\Pages\Dashboard::getUrl(panel: $panel);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }
}
