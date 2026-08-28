<?php

namespace App\Filament\Resources\PressPosts\Pages;

use App\Filament\Resources\PressPosts\PressPostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPressPost extends EditRecord
{
    protected static string $resource = PressPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['translations'])) {
            foreach ($data['translations'] as &$translation) {
                $translation['updated_by'] = auth()->id();
            }
        }

        return $data;
    }
}