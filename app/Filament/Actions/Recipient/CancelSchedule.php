<?php

namespace App\Filament\Actions\Recipient;

use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class CancelSchedule extends Action
{
    use VisibleForStatus;

    public static function getDefaultName(): ?string
    {
        return 'recipient-cancel-schedule';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Cancel schedule');
        $this->color('primary');
        $this->icon(Heroicon::NoSymbol);

        $this->action(fn(Recipient $r) => $r->cancelSchedule());
    }
}
