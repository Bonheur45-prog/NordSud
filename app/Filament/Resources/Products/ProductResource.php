<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Filament\Support\DedicatedPageRegistry;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static bool $shouldRegisterNavigation = false;

    /**
     * Bank Card and Secured Documents each have a dedicated, hand-built
     * page editor with a content schema matched to their real, richer
     * structure — see App\Filament\Pages\PageContent\BankCardPage and
     * SecuredDocumentsPage. This resource's generic form uses a different,
     * simpler shape, so those slugs are EXCLUDED here to prevent the
     * generic form from silently overwriting their real content on save.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('template', 'product')
            ->whereNotIn('slug', DedicatedPageRegistry::productSlugs());
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return \Filament\Pages\Dashboard::getUrl(panel: $panel);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
