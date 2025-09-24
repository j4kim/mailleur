<?php

namespace App\Filament\Actions\Bulk;

use App\Models\Recipient;
use Filament\Actions\BulkAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class GenerateRecipientsBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Generate / Regenerate selected");

        $this->color('primary');

        $this->icon(Heroicon::Bolt);

        $this->requiresConfirmation();

        $this->action(function (Collection $records) {
            $records->each(fn(Recipient $r) => $r->generateAndSave());
        });
    }
}
