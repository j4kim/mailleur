<?php

namespace App\Filament\Actions;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Icons\Heroicon;

class GenerateAction extends EditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Generate");

        $this->icon(Heroicon::Bolt);

        $this->schema([
            RichEditor::make('mail_body')
        ]);

        $this->mutateRecordDataUsing(
            fn(Recipient $recipient): array =>
            ['mail_body' => $recipient->generateMailBody()]
        );

        $this->mutateDataUsing(function (array $data): array {
            $data['status'] = RecipientStatus::Customized;
            return $data;
        });

        $this->slideOver();
    }
}
