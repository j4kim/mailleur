<?php

namespace App\Filament\Actions\Recipient;

use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class Schedule extends Action
{
    use VisibleForStatus;

    public static function getDefaultName(): ?string
    {
        return 'recipient-schedule';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Schedule');
        $this->color('primary');
        $this->icon(Heroicon::Clock);
        $this->modalWidth(Width::Small);

        $this->schema([
            DateTimePicker::make('to_be_sent_at')
                ->belowContent('Minutes must be set to 0, 15, 30 or 45')
                ->native(false)
                ->minutesStep(15)
                ->minDate(now())
                ->seconds(false)
                ->default(now()->startOfDay()->nextWeekday()->hour(8))
                ->displayFormat('d.m.Y H:i'),
        ]);

        $this->action(fn(Recipient $r, array $data) => $r->schedule($data['to_be_sent_at']));
    }
}
