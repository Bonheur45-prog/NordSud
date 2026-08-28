<?php

namespace App\Filament\Resources\TranslationStrings\Pages;

use App\Filament\Resources\TranslationStrings\TranslationStringResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTranslationString extends CreateRecord
{
    protected static string $resource = TranslationStringResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}