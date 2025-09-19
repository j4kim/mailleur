<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Filament\Infolists\Components\RichTextEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

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
            ]);
    }
}
