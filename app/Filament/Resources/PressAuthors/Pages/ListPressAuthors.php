<?php

namespace App\Filament\Resources\PressAuthors\Pages;

use App\Filament\Resources\PressAuthors\PressAuthorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPressAuthors extends ListRecords
{
    protected static string $resource = PressAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
