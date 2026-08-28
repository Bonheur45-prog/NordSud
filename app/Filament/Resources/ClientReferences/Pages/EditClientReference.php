<?php

namespace App\Filament\Resources\ClientReferences\Pages;

use App\Filament\Resources\ClientReferences\ClientReferenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientReference extends EditRecord
{
    protected static string $resource = ClientReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
