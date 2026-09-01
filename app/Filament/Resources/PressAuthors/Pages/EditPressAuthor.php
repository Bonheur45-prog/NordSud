<?php

namespace App\Filament\Resources\PressAuthors\Pages;

use App\Filament\Resources\PressAuthors\PressAuthorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPressAuthor extends EditRecord
{
    protected static string $resource = PressAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
