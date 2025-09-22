<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
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
            ])->disabled(!$this->iAmAdmin);
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->hidden(!$this->iAmAdmin);;
    }
}
