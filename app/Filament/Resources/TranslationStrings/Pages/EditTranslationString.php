<?php

namespace App\Filament\Resources\TranslationStrings\Pages;

use App\Filament\Resources\TranslationStrings\TranslationStringResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTranslationString extends EditRecord
{
    protected static string $resource = TranslationStringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}