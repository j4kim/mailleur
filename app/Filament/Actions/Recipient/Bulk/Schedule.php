<?php

namespace App\Filament\Actions\Recipient\Bulk;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

use function App\Tools\errorNotif;
use function App\Tools\notif;
use function App\Tools\successNotif;

class Schedule extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-bulk-schedule';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Schedule selected");
        $this->icon(Heroicon::Clock);
        $this->color('primary');

        $this->requiresConfirmation();

        $this->modalDescription(
            "This will schedule the mail for selected recipients to be sent at the given date and time.
            Recipients with status \"Sent\" will be ignored.
            Are you sure you want to do this?"
        );

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

        $this->action(function (Collection $records, array $data) {
            $recipients = $records->where('status', '!==', RecipientStatus::Sent);
            foreach ($recipients as $recipient) {
                /** @var Recipient $recipient */
                $recipient->schedule($data['to_be_sent_at']);
            }
            $count = $recipients->count();
            if ($count) {
                successNotif("Mail scheduled for " . str("recipient")->plural($recipients->count(), true));
            } else {
                notif(null, "No recipients to schedule")->warning()->send();
            }
        });
    }
}
