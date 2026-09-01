<?php

namespace App\Filament\Resources\PressPageSettings\Pages;

use App\Filament\Resources\PressPageSettingsResource;
use App\Models\PressPageSettings;
use Filament\Resources\Pages\EditRecord;

class EditSettings extends EditRecord
{
    protected static string $resource = PressPageSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(string|int $record = null): void
    {
        // Always load the first (and only) settings record
        $this->record = PressPageSettings::firstOrCreate(
            ['id' => 1],
            ['created_at' => now(), 'updated_at' => now()]
        );

        parent::mount($this->record->getKey());
    }
}
