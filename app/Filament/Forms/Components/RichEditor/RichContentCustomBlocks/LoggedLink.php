<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class LoggedLink extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'logged-link';
    }

    public static function getLabel(): string
    {
        return 'Logged link';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->schema([
                TextInput::make('text')->required(),
                TextInput::make('url')->url()->required(),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view(
            'filament.custom-blocks.logged-link-preview',
            $config
        )->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view(
            'filament.custom-blocks.logged-link',
            compact('config', 'data')
        )->render();
    }
}
