<?php

namespace App\Filament\Actions\Recipient;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Icons\Heroicon;

class Generate extends EditAction
{
    use VisibleForStatus;

    public static function getDefaultName(): ?string
    {
        return 'recipient-generate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(
            fn(Recipient $recipient) =>
            $recipient->status === RecipientStatus::Initial ? "Generate" : "Regenerate"
        );
        $this->icon(Heroicon::Bolt);
        $this->slideOver();
        $this->closeModalByClickingAway(false);

        $this->schema([
            RichEditor::make('mail_body')->json()
        ]);

        $this->mutateRecordDataUsing(
            function (Recipient $recipient) {
                $mailBody = $recipient->generateMailBody();
                return ['mail_body' => $mailBody];
            }
        );

        $this->mutateDataUsing(function (array $data): array {
            $data['status'] = RecipientStatus::Customized;
            return $data;
        });
    }
}
