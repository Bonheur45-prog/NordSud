<?php

namespace App\Filament\Resources\PressTags\Pages;

use App\Filament\Resources\PressTags\PressTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPressTags extends ListRecords
{
    protected static string $resource = PressTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
