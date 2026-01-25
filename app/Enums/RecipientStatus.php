<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RecipientStatus: int implements HasLabel, HasColor
{
    case Initial = 0;
    case Customized = 1;
    case Ready = 2;
    case Sent = 3;
    case Failed = 4;
    case Scheduled = 5;

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Initial => 'gray',
            self::Customized => 'warning',
            self::Ready => 'info',
            self::Sent => 'success',
            self::Failed => 'danger',
            self::Scheduled => Color::Fuchsia,
        };
    }
}
