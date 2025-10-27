<?php

namespace App\Filament\Actions\Recipient;

use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

use function App\Tools\formatAddress;
use function App\Tools\renderProse;

class Logs extends Action
{
    use VisibleForStatus;

    public static function getDefaultName(): ?string
    {
        return 'recipient-log';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Logs");
        $this->modalWidth(Width::FiveExtraLarge);
        $this->icon(Heroicon::Bars3);

        $this->schema([
            RepeatableEntry::make('eventLogs')
                ->hiddenLabel()
                ->schema([
                    TextEntry::make('created_at')->dateTime('d.m.Y H:i')->hiddenLabel()->columnSpan([
                        'default' => 6,
                        'md' => 4,
                        'lg' => 2,
                    ]),
                    TextEntry::make('user.name')->hiddenLabel()->columnSpan([
                        'default' => 6,
                        'md' => 4,
                        'lg' => 2,
                    ]),
                    TextEntry::make('type')->badge()->hiddenLabel()->columnSpan([
                        'default' => 6,
                        'md' => 4,
                        'lg' => 2,
                    ]),
                ])
                ->columns(['default' => 12])
        ]);

        $this->modalSubmitAction(false);
        $this->modalCancelAction(false);
    }
}
