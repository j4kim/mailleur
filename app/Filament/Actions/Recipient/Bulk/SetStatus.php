<?php

namespace App\Filament\Actions\Recipient\Bulk;

use App\Enums\RecipientStatus;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class SetStatus extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-bulk-set-status';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Set status for selected");
        $this->icon(Heroicon::Tag);
        $this->color('primary');

        $this->schema([
            ToggleButtons::make('status')->options(RecipientStatus::class)
        ]);

        $this->action(function (Collection $records, array $data) {
            $records->each(fn($record) => $record->update($data));
        });
    }
}
