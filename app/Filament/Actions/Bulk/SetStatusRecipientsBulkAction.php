<?php

namespace App\Filament\Actions\Bulk;

use App\Enums\RecipientStatus;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class SetStatusRecipientsBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Set status for selected");

        $this->color('primary');

        $this->icon(Heroicon::Tag);

        $this->schema([
            ToggleButtons::make('status')->options(RecipientStatus::class)
        ]);

        $this->action(function (Collection $records, array $data) {
            $records->each(fn($record) => $record->update($data));
        });
    }
}
