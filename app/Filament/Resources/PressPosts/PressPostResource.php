<?php

namespace App\Filament\Resources\PressPosts;

use App\Filament\Resources\PressPosts\Pages\CreatePressPost;
use App\Filament\Resources\PressPosts\Pages\EditPressPost;
use App\Filament\Resources\PressPosts\Pages\ListPressPosts;
use App\Filament\Resources\PressPosts\Schemas\PressPostForm;
use App\Filament\Resources\PressPosts\Tables\PressPostsTable;
use App\Models\PressPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PressPostResource extends Resource
{
    protected static ?string $model = PressPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        return PressPostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PressPostsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPressPosts::route('/'),
            'create' => CreatePressPost::route('/create'),
            'edit' => EditPressPost::route('/{record}/edit'),
        ];
    }
}
