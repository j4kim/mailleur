<?php

namespace App\Filament\Resources\MailTemplates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MailTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('body')
                    ->columnSpanFull(),
            ]);
    }
}
