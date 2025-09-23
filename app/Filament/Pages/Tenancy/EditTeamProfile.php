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
                    ->hidden(!$iAmAdmin),
                Section::make('Envelope defaults')
                    ->columns(['sm' => 2])
                    ->schema([
                        TextInput::make('defaults.from.address')
                            ->label("From address")
                            ->hint("Must be on same domain as SMTP username")
                            ->email(),
                        TextInput::make('defaults.from.name')
                            ->label("From name"),
                        TextInput::make('defaults.replyTo'),
                        null,
                        Repeater::make('defaults.cc')
                            ->simple(
                                TextInput::make('email')->email()->required()
                            ),
                        Repeater::make('defaults.bcc')
                            ->simple(
                                TextInput::make('email')->email()->required()
                            ),
                    ])
                    ->collapsed(),
            ])->disabled(!$iAmAdmin);
    }

    protected function getSaveFormAction(): Action
    {
        $iAmAdmin = Auth::user()->isAdminOf($this->tenant);
        return parent::getSaveFormAction()->hidden(!$iAmAdmin);;
    }
}
