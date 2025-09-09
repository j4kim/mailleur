<?php

namespace App\Filament\Resources\MailTemplates\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class MailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('body')
                    ->mergeTags([
                        'name',
                    ])
                    ->json()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
