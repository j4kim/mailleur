<?php

namespace App\Filament\Actions\Recipient;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;

class Write extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-write';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Write");
        $this->slideOver();

        $this->schema([
            RichEditor::make('mail_body')
        ]);
    }
}
