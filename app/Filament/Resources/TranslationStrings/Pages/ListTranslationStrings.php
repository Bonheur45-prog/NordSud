<?php

namespace App\Filament\Resources\TranslationStrings\Pages;

use App\Filament\Resources\TranslationStrings\TranslationStringResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTranslationStrings extends ListRecords
{
    protected static string $resource = TranslationStringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
