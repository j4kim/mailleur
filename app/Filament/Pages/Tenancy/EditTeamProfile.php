<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EditTeamProfile extends EditTenantProfile
{
    protected string $view = 'filament.edit-team';

    public static function getLabel(): string
    {
        return 'Team settings';
    }

    public static function getEnvelopeSchema(string $baseKey): array
    {
        return [
            TextInput::make("$baseKey.from.address")
                ->label("From address")
                ->belowContent(
                    "Must be on same domain as SMTP username. " .
                        "On Infomaniak, this must be exatly the username. "
                )
                ->email(),
            TextInput::make("$baseKey.from.name")
                ->label("From name"),
            TextInput::make("$baseKey.replyTo.address")
                ->label("Reply to address")
                ->email(),
            TextInput::make("$baseKey.replyTo.name")
                ->label("Reply to name"),
            Repeater::make("$baseKey.cc")->schema([
                TextInput::make('address')->email()->required(),
                TextInput::make('name'),
            ])->defaultItems(0)->columns(2),
            Repeater::make("$baseKey.bcc")->schema([
                TextInput::make('address')->email()->required(),
                TextInput::make('name'),
            ])->defaultItems(0)->columns(2),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $iAmAdmin = Auth::user()->isAdminOf($this->tenant);
        return $schema
            ->components([
                TextInput::make('name'),

                Section::make('SMTP config')
                    ->columns(['sm' => 2])
                    ->schema([
                        TextInput::make('smtp_config.host'),
                        TextInput::make('smtp_config.port')
                            ->integer(),
                        TextInput::make('smtp_config.username')
                            ->email()
                            ->hint("Must be an email"),
                        TextInput::make('smtp_config.password')
                            ->password()
                            ->revealable()
                            ->hint("The email address password"),
                    ])
                    ->collapsed()
                    ->persistCollapsed()
                    ->hidden(!$iAmAdmin),

                Section::make('Defaults')
                    ->schema([
                        Section::make('Envelope')
                            ->columns(2)
                            ->schema(self::getEnvelopeSchema("defaults.envelope"))
                            ->secondary()
                            ->compact()
                    ])
                    ->collapsed()
                    ->persistCollapsed(),

            ])->disabled(!$iAmAdmin);
    }

    protected function getSaveFormAction(): Action
    {
        $iAmAdmin = Auth::user()->isAdminOf($this->tenant);
        return parent::getSaveFormAction()->hidden(!$iAmAdmin);;
    }
}
