<?php

namespace App\Filament\Resources\PressAuthors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PressAuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->helperText('A proper noun — not translated.'),
                                FileUpload::make('avatar')
                                    ->image()
                                    ->avatar()
                                    ->directory('press/authors'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('role_fr')
                                    ->label('Role (Français)')
                                    ->placeholder('ex. Responsable Marketing'),
                                TextInput::make('role_en')
                                    ->label('Role (English)')
                                    ->placeholder('e.g. Marketing Lead'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Textarea::make('bio_fr')
                                    ->label('Bio (Français)')
                                    ->rows(3),
                                Textarea::make('bio_en')
                                    ->label('Bio (English)')
                                    ->rows(3),
                            ]),
                    ]),
                Section::make('Social links')
                    ->description('Shown as icons on the author card. Leave blank to hide.')
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
}
