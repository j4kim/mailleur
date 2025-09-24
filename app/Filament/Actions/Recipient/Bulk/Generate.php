<?php

namespace App\Filament\Actions\Recipient\Bulk;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class Generate extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-bulk-generate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Generate / Regenerate selected");
        $this->icon(Heroicon::Bolt);
        $this->color('primary');

        $this->requiresConfirmation();

        $this->action(function (Collection $records) {
            $records->each(fn(Recipient $r) => $r->generateAndSave());
        });
    }
}
