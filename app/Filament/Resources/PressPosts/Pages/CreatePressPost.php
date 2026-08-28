<?php

namespace App\Filament\Resources\PressPosts\Pages;

use App\Filament\Resources\PressPosts\PressPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePressPost extends CreateRecord
{
    protected static string $resource = PressPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['translations'])) {
            foreach ($data['translations'] as &$translation) {
                $translation['updated_by'] = auth()->id();
            }
        }

        return $data;
    }
}