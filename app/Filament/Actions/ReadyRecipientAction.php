<?php

namespace App\Filament\Actions;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class ReadyRecipientAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Mark as Ready");

        $this->visible(fn(Recipient $r) => $r->status === RecipientStatus::Customized);

        $this->color('primary');

        $this->icon(Heroicon::Check);

        $this->action(function (Recipient $r) {
            $r->status = RecipientStatus::Ready;
            $r->save();
        });
    }
}
