<?php

namespace App\Filament\Actions\Recipient;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Ready extends Action
{
    use VisibleForStatus;

    public static function getDefaultName(): ?string
    {
        return 'recipient-ready';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Mark as Ready");
        $this->color('primary');
        $this->icon(Heroicon::Check);

        $this->action(function (Recipient $r) {
            $r->status = RecipientStatus::Ready;
            $r->save();
        });
    }
}
