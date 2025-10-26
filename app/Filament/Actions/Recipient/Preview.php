<?php

namespace App\Filament\Actions\Recipient;

use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

use function App\Tools\formatAddress;
use function App\Tools\renderProse;

class Preview extends Action
{
    use VisibleForStatus;

    public static function getDefaultName(): ?string
    {
        return 'recipient-preview';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Preview");
        $this->color('primary');
        $this->icon(Heroicon::Eye);

        $this->schema([
            TextEntry::make('campaign.subject'),
            TextEntry::make('campaign.from')->state(
                fn(Recipient $r) => formatAddress((array) $r->campaign->getFrom())
            ),
            Section::make()
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('mail_body')
                        ->state(fn(Recipient $r) => renderProse($r->mail_body))
                        ->hiddenLabel()
                        ->html()
                ]),
        ]);

        $this->modalSubmitAction(false);
        $this->modalCancelAction(false);
    }
}
