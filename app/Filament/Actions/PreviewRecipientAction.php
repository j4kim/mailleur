<?php

namespace App\Filament\Actions;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

use function App\Tools\formatAddress;
use function App\Tools\prose;

class PreviewRecipientAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Preview");

        $this->visible(fn(Recipient $r) => $r->status == RecipientStatus::Sent);

        $this->schema([
            TextEntry::make('campaign.subject'),
            TextEntry::make('campaign.from')->state(
                fn(Recipient $r) => formatAddress((array) $r->campaign->getFrom())
            ),
            Section::make()
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('mail_body')
                        ->state(fn(Recipient $r) => prose($r->mail_body))
                        ->hiddenLabel()
                        ->html()
                ]),
        ]);

        $this->modalSubmitAction(false);
        $this->modalCancelAction(false);
    }
}
