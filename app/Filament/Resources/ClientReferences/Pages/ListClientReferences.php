<?php

namespace App\Filament\Resources\ClientReferences\Pages;

use App\Filament\Resources\ClientReferences\ClientReferenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientReferences extends ListRecords
{
    protected static string $resource = ClientReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
