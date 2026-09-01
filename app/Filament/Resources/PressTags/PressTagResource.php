<?php

namespace App\Filament\Resources\PressTags;

use App\Filament\Resources\PressTags\Pages\CreatePressTag;
use App\Filament\Resources\PressTags\Pages\EditPressTag;
use App\Filament\Resources\PressTags\Pages\ListPressTags;
use App\Filament\Resources\PressTags\Schemas\PressTagForm;
use App\Filament\Resources\PressTags\Tables\PressTagsTable;
use App\Models\PressTag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PressTagResource extends Resource
{
    protected static ?string $model = PressTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 102;

    protected static ?string $recordTitleAttribute = 'name_fr';

    protected static ?string $navigationLabel = 'Press Tags';

    public static function form(Schema $schema): Schema
    {
        return PressTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PressTagsTable::configure($table);
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
            'index' => ListPressTags::route('/'),
            'create' => CreatePressTag::route('/create'),
            'edit' => EditPressTag::route('/{record}/edit'),
        ];
    }
}
