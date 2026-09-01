<?php

namespace App\Filament\Resources\PressPosts\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Comments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('author_name')
                            ->label('Name')
                            ->required(),
                        TextInput::make('author_email')
                            ->label('Email')
                            ->email()
                            ->helperText('Only shown in /admin — never rendered on the public site.'),
                    ]),
                Textarea::make('body')
                    ->label('Comment')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Grid::make(2)
                    ->schema([
                        DateTimePicker::make('commented_at')
                            ->label('Posted on')
                            ->default(now())
                            ->helperText('Lets you backdate a comment when migrating content from elsewhere.'),
                        Toggle::make('approved')
                            ->label('Approved (visible on the public site)')
                            ->helperText('Comments submitted through the public form start unapproved — review them here before they go live.')
                            ->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                TextColumn::make('author_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('body')
                    ->limit(60)
                    ->wrap(),
                IconColumn::make('approved')
                    ->boolean()
                    ->label('Approved'),
                TextColumn::make('commented_at')
                    ->label('Posted on')
                    ->dateTime('j M Y')
                    ->sortable(),
            ])
            ->defaultSort('commented_at', 'desc')
            ->filters([
                TernaryFilter::make('approved'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
