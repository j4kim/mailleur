<?php

namespace App\Filament\Actions\Recipient;

use App\Enums\RecipientStatus;
use App\Models\Recipient;
use Exception;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

use function App\Tools\errorNotif;
use function App\Tools\successNotif;

class Send extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'recipient-send';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::PaperAirplane);

        $this->action(function (Recipient $r) {
            try {
                $r->sendOne();
                successNotif("Mail sent to $r->email");
            } catch (Exception $e) {
                errorNotif($e->getMessage());
            }
        });

        $this->visible(fn(Recipient $r) => in_array($r->status, [
            RecipientStatus::Customized,
            RecipientStatus::Ready
        ]));

        $this->color('success');
    }
}
