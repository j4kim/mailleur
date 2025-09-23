<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
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

                Section::make('Envelope')
                    ->columns(['sm' => 2])
                    ->schema([
                        TextInput::make('envelope.from.address')
                            ->label("From address")
                            ->hint("Must be on same domain as SMTP username")
                            ->email(),
                        TextInput::make('envelope.from.name')
                            ->label("From name"),
                        TextInput::make('envelope.replyTo.address')
                            ->label("Reply to address")
                            ->email(),
                        TextInput::make('envelope.replyTo.name')
                            ->label("Reply to name"),
                        Repeater::make('envelope.cc')->simple(
                            TextInput::make('email')->email()->required()
                        ),
                        Repeater::make('envelope.bcc')->simple(
                            TextInput::make('email')->email()->required()
                        ),
                    ])
                    ->columnSpanFull()
                    ->collapsed()
                    ->persistCollapsed(),
            ]);
    }
}
