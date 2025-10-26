<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventLogType: string implements HasLabel, HasColor
{
    case LinkClicked = 'link-clicked';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::LinkClicked => 'success',
        };
    }
}
