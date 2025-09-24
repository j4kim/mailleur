<?php

namespace App\Filament\Actions;

use App\Enums\RecipientStatus;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;

class SetStatusRecipientAction extends EditAction
{
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
