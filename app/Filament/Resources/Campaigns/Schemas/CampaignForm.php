<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->required(),
                RichEditor::make('template')
                    ->hiddenOn('create')
                    ->mergeTags(function (Campaign $campaign) {
                        return ['email', ...$campaign->columns];
                    })
                    ->json()
                    ->columnSpanFull(),
            ]);
    }
}
