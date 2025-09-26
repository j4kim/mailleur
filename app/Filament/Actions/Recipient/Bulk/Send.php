<?php

namespace App\Filament\Actions\Recipient\Bulk;

use App\Models\Recipient;
use Filament\Actions\BulkAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

use function App\Tools\errorNotif;
use function App\Tools\notif;
use function App\Tools\successNotif;

class Send extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-bulk-send';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Send selected");
        $this->icon(Heroicon::PaperAirplane);
        $this->color('primary');

        $this->requiresConfirmation();

        $this->modalDescription(
            "This will\nbuild mail body for recipients with status \"Initial\", 
            and send mail for all recipients with status other than \"Sent\". 
            Are you sure you would like to do this?"
        );

        $this->action(function (Collection $records) {
            [$successCount, $errorCount] = Recipient::sendMany($records);
            if ($successCount) {
                successNotif("Mail sent to " . str("recipient")->plural($successCount, true));
            }
            if ($errorCount) {
                errorNotif("Sending failed for " . str("recipient")->plural($errorCount, true));
            }
            if ($successCount + $errorCount === 0) {
                notif(null, "No mails sent")->warning()->send();
            }
        });
    }
}
