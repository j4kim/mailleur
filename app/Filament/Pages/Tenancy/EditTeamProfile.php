<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EditTeamProfile extends EditTenantProfile
{
    protected string $view = 'filament.edit-team';

    private bool $iAmAdmin;

    public static function getLabel(): string
    {
        return 'Team settings';
    }

    public function beforeFill()
    {
        $this->iAmAdmin = Auth::user()->isAdminOf($this->tenant);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                Section::make('SMTP config')
                    ->columns([
                        'sm' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        TextInput::make('host'),
                        TextInput::make('port'),
                        TextInput::make('username'),
                        TextInput::make('password'),
                    ])
                    ->hidden(!$this->iAmAdmin),
            ])->disabled(!$this->iAmAdmin);
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->hidden(!$this->iAmAdmin);;
    }
}
