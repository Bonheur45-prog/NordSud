<?php

namespace App\Filament\Resources\PressTags\Pages;

use App\Filament\Resources\PressTags\PressTagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPressTag extends EditRecord
{
    protected static string $resource = PressTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
