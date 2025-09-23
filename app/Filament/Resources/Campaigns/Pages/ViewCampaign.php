<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Actions\DuplicateCampaignAction;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Filament\Resources\Campaigns\RelationManagers\RecipientsRelationManager;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            ActionGroup::make([
                DuplicateCampaignAction::make(),
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
