<?php

namespace App\Filament\Actions\Recipient;

use App\Enums\RecipientStatus;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;

class SetStatus extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-set-status';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Set status");

        $this->icon(Heroicon::Tag);

        $this->schema([
            ToggleButtons::make('status')->options(RecipientStatus::class)
        ]);
    }
}
