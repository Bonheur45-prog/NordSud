<?php

namespace App\Filament\Resources\FooterSettings\Pages;

use App\Filament\Resources\FooterSettingsResource;
use App\Models\FooterSettings;
use Filament\Resources\Pages\EditRecord;

class EditSettings extends EditRecord
{
    protected static string $resource = FooterSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(string|int $record = null): void
    {
        // Always load the first (and only) settings record — same
        // singleton pattern as Press Page Settings.
        $this->record = FooterSettings::firstOrCreate(
            ['id' => 1],
            ['created_at' => now(), 'updated_at' => now()]
        );

        parent::mount($this->record->getKey());
    }
}
