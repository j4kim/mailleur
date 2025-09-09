<?php

namespace App\Filament\Resources\MailTemplates\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
