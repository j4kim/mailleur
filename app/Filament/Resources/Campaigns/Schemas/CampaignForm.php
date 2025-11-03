<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Models\Campaign;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                        return $campaign->getMergeTags();
                    })
                    ->json()
                    ->columnSpanFull(),

                TagsInput::make('columns')
                    ->hiddenOn('create')
                    ->placeholder("Add column")
                    ->belowContent("Columns are extra attributes attached to each recipient. They can be used as merge tags in templates, to render dynamic content customized for the recipient."),

                Toggle::make('enable_logged_links')
                    ->belowContent("Enable this option if you want to know which recipient clicks on the links in the message."),

                Section::make('Envelope')
                    ->columns(2)
                    ->schema(EditTeamProfile::getEnvelopeSchema("envelope"))
                    ->hiddenOn('create')
                    ->columnSpanFull()
                    ->collapsed()
                    ->persistCollapsed(),
            ]);
    }
}
