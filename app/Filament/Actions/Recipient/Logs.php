<?php

namespace App\Filament\Actions\Recipient;

use App\Enums\EventLogType;
use App\Models\EventLog;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Contracts\View\View;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

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
                    TextEntry::make('created_at')
                        ->dateTime('d.m.Y H:i')
                        ->hiddenLabel()
                        ->columnSpan(['default' => 6, 'md' => 4, 'lg' => 2]),
                    TextEntry::make('user.name')
                        ->hiddenLabel()
                        ->columnSpan(['default' => 6, 'md' => 4, 'lg' => 2]),
                    TextEntry::make('type')
                        ->badge()
                        ->hiddenLabel()
                        ->columnSpan(['default' => 6, 'md' => 4, 'lg' => 2]),
                    TextEntry::make('details')
                        ->state(fn(EventLog $eventLog): View => view(
                            'filament.event-log-details',
                            ['eventLog' => $eventLog],
                        ))
                        ->hiddenLabel()
                        ->columnSpan(['default' => 12, 'md' => 12, 'lg' => 6])
                        ->visible(fn(EventLog $eventLog) => in_array($eventLog->type, [
                            EventLogType::StatusChanged
                        ])),
                ])
                ->columns(['default' => 12])
        ]);

        $this->modalSubmitAction(false);
        $this->modalCancelAction(false);
    }
}
