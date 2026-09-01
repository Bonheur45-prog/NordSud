<?php

namespace App\Filament\Resources\PressAuthors;

use App\Filament\Resources\PressAuthors\Pages\CreatePressAuthor;
use App\Filament\Resources\PressAuthors\Pages\EditPressAuthor;
use App\Filament\Resources\PressAuthors\Pages\ListPressAuthors;
use App\Filament\Resources\PressAuthors\Schemas\PressAuthorForm;
use App\Filament\Resources\PressAuthors\Tables\PressAuthorsTable;
use App\Models\PressAuthor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PressAuthorResource extends Resource
{
    protected static ?string $model = PressAuthor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?int $navigationSort = 101;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Press Authors';

    public static function form(Schema $schema): Schema
    {
        return PressAuthorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PressAuthorsTable::configure($table);
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
            'index' => ListPressAuthors::route('/'),
            'create' => CreatePressAuthor::route('/create'),
            'edit' => EditPressAuthor::route('/{record}/edit'),
        ];
    }
}
