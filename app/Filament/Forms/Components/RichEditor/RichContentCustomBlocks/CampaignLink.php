<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class CampaignLink extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'campaign-link';
    }

    public static function getLabel(): string
    {
        return 'Link';
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
            'filament.forms.components.rich-editor.rich-content-custom-blocks.campaign-link.preview',
            $config
        )->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view(
            'filament.forms.components.rich-editor.rich-content-custom-blocks.campaign-link.index',
            compact('config', 'data')
        )->render();
    }
}
