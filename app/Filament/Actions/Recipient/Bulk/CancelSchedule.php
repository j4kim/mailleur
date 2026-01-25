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

class CancelSchedule extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-bulk-cancel-schedule';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Cancel schedule for selected");
        $this->icon(Heroicon::NoSymbol);
        $this->color('primary');

        $this->requiresConfirmation();

        $this->modalDescription(
            "This will cancel the schedule for the selected recipients with status \"Scheduled\".
            Are you sure you want to do this?"
        );

        $this->action(function (Collection $records) {
            $recipients = $records->where('status', RecipientStatus::Scheduled);
            foreach ($recipients as $recipient) {
                /** @var Recipient $recipient */
                $recipient->cancelSchedule();
            }
            $count = $recipients->count();
            if ($count) {
                successNotif("Schedule cancelled for " . str("recipient")->plural($recipients->count(), true));
            } else {
                notif(null, "No recipients with status Scheduled selected")->warning()->send();
            }
        });
    }
}
