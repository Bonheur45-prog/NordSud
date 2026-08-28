<?php

namespace App\Filament\Resources\ClientReferences;

use App\Filament\Resources\ClientReferences\Pages\CreateClientReference;
use App\Filament\Resources\ClientReferences\Pages\EditClientReference;
use App\Filament\Resources\ClientReferences\Pages\ListClientReferences;
use App\Models\ClientReference;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ClientReferenceResource extends Resource
{
    protected static ?string $model = ClientReference::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $navigationLabel = 'Reference Logos';

    protected static string|UnitEnum|null $navigationGroup = 'About';

    protected static ?int $navigationSort = 19;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Institution name')
                        ->required(),
                    TextInput::make('order')
                        ->label('Display order')
                        ->numeric()
                        ->required()
                        ->default(0),
                ]),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('client-references')
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                ImageColumn::make('logo')->label('Logo'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('order')->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientReferences::route('/'),
            'create' => CreateClientReference::route('/create'),
            'edit' => EditClientReference::route('/{record}/edit'),
        ];
    }
}
