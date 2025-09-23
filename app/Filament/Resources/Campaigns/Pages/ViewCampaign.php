<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Enums\RecipientStatus;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Filament\Resources\Campaigns\RelationManagers\RecipientsRelationManager;
use App\Models\Campaign;
use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

use function App\Tools\successNotif;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            ActionGroup::make([
                Action::make('duplicate')
                    ->icon(Heroicon::Square2Stack)
                    ->schema([
                        TextInput::make('subject')->default($this->record->subject)->required(),
                        Checkbox::make('duplicate_recipients'),
                    ])
                    ->action(function (array $data, Component $livewire): void {
                        /** @var Campaign $replica */
                        $replica = $this->record->replicate()->fill([
                            'subject' => $data['subject'],
                        ]);
                        $replica->save();
                        if ($data['duplicate_recipients']) {
                            $replica->recipients()->createMany(
                                $this->record->recipients->map(function (Recipient $recipient) {
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
                            $this->getResource()::getUrl('view', ['record' => $replica])
                        );
                        successNotif("Campaign duplicated");
                    }),
            ]),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            RecipientsRelationManager::class,
        ];
    }
}
