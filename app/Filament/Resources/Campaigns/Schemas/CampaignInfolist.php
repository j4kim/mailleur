<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Filament\Infolists\Components\RichTextEntry;
use App\Models\Campaign;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use function App\Tools\formatAddress;

class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
                TextEntry::make('subject'),
                TextEntry::make('columns'),
                RichTextEntry::make('template')
                    ->columnSpanFull(),

                Section::make('Envelope')
                    ->columns(2)
                    ->schema([
                        TextEntry::make("envelope.from")->state(
                            fn(Campaign $c) => formatAddress($c->envelope['from'])
                        ),
                        TextEntry::make("envelope.replyTo")->state(
                            fn(Campaign $c) => formatAddress($c->envelope['replyTo'])
                        ),
                        TextEntry::make("envelope.cc")->formatStateUsing(
                            fn(array $state) => formatAddress($state)
                        )->listWithLineBreaks(),
                        TextEntry::make("envelope.bcc")->formatStateUsing(
                            fn(array $state) => collect($state)->filter()->join(", ")
                        )->listWithLineBreaks(),
                    ])
                    ->collapsed()
                    ->persistCollapsed()
                    ->columnSpanFull(),
            ]);
    }
}
