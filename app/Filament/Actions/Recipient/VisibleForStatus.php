<?php

namespace App\Filament\Actions\Recipient;

use App\Enums\RecipientStatus;
use App\Models\Recipient;

trait VisibleForStatus
{
    public function for(RecipientStatus ...$status): static
    {
        return $this->visible(fn(Recipient $r) => in_array($r->status, $status));
    }
}
