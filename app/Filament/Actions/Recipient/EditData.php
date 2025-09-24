<?php

namespace App\Filament\Actions\Recipient;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->label("Edit data");

        $this->schema(function (Recipient $recipient) {
            return [
                Grid::make(2)->schema([
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    ...collect($recipient->campaign->columns)->map(function (string $name) {
                        return Textarea::make("data.$name")->label($name)->rows(1);
                    }),
                ])
            ];
        });
    }
}
