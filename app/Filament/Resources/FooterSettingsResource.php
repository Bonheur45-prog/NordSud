<?php

namespace App\Filament\Resources;

use App\Models\FooterSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FooterSettingsResource extends Resource
{
    protected static ?string $model = FooterSettings::class;

    protected static ?string $navigationLabel = 'Footer Settings';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'footer-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?int $navigationSort = 200;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Social links')
                    ->description('Shown as icons under the logo in the site footer. Leave a field blank to hide that icon — the phone numbers, addresses, and nav columns elsewhere in the footer are already editable from Contact (Contact par pays) and the site\'s own pages, so this only covers what was previously hardcoded.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('facebook_url')->label('Facebook')->url(),
                                TextInput::make('instagram_url')->label('Instagram')->url(),
                                TextInput::make('x_url')->label('X (Twitter)')->url(),
                                TextInput::make('linkedin_url')->label('LinkedIn')->url(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\FooterSettings\Pages\EditSettings::route('/'),
        ];
    }
}
