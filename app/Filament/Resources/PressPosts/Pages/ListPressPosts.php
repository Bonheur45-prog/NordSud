<?php

namespace App\Filament\Resources\PressPosts\Pages;

use App\Filament\Resources\PressPosts\PressPostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPressPosts extends ListRecords
{
    protected static string $resource = PressPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
