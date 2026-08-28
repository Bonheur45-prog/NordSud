<?php

namespace App\Filament\Resources\TranslationStrings;

use App\Filament\Resources\TranslationStrings\Pages\CreateTranslationString;
use App\Filament\Resources\TranslationStrings\Pages\EditTranslationString;
use App\Filament\Resources\TranslationStrings\Pages\ListTranslationStrings;
use App\Filament\Resources\TranslationStrings\Schemas\TranslationStringForm;
use App\Filament\Resources\TranslationStrings\Tables\TranslationStringsTable;
use App\Models\TranslationString;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TranslationStringResource extends Resource
{
    protected static ?string $model = TranslationString::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 120;

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Schema $schema): Schema
    {
        return TranslationStringForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TranslationStringsTable::configure($table);
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
            'index' => ListTranslationStrings::route('/'),
            'create' => CreateTranslationString::route('/create'),
            'edit' => EditTranslationString::route('/{record}/edit'),
        ];
    }
}
