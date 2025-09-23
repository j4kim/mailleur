<?php

namespace App\Filament\Actions;

use App\Enums\RecipientStatus;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

use function App\Tools\successNotif;

class DuplicateCampaignAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'duplicate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Duplicate");

        $this->icon(Heroicon::Square2Stack);

        $this->schema([
            TextInput::make('subject')
                ->default(fn(Model $record) => $record->subject)
                ->required(),
            Checkbox::make('duplicate_recipients'),
        ]);

        $this->action(function (array $data, Component $livewire, Model $record): void {
            /** @var Campaign $replica */
            $replica = $record->replicate()->fill([
                'subject' => $data['subject'],
            ]);
            $replica->save();
            if ($data['duplicate_recipients']) {
                $replica->recipients()->createMany(
                    $record->recipients->map(function (Recipient $recipient) {
                        $body = $recipient->mail_body;
                        return [
                            'email' => $recipient->email,
                            'data' => $recipient->data,
                            'mail_body' => $body,
                            'status' => $body ? RecipientStatus::Customized : RecipientStatus::Initial,
                        ];
                    })
                );
            }
            $livewire->redirect(
                CampaignResource::getUrl('view', ['record' => $replica])
            );
            successNotif("Campaign duplicated");
        });
    }
}
