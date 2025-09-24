<?php

namespace App\Filament\Actions\Recipient;

use App\Models\Campaign;
use App\Models\Recipient;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

class EditData extends EditAction
{
    use VisibleForStatus;

    public static function getDefaultName(): ?string
    {
        return 'recipient-edit-data';
    }

    public static function getCustomSchema(Campaign $campaign): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                ...collect($campaign->columns)->map(function (string $name) {
                    return Textarea::make("data.$name")->label($name)->rows(1);
                }),
            ])
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Edit data");

        $this->schema(function (Recipient $recipient) {
            return self::getCustomSchema($recipient->campaign);
        });
    }
}
