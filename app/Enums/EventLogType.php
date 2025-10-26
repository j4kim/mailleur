<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventLogType: string implements HasLabel, HasColor
{
    case CampaignCreated = 'campaign-created';
    case RecipientCreated = 'recipient-created';
    case RecipientImported = 'recipient-imported';
    case MailSent = 'mail-sent';
    case SendingFailed = 'sending-failed';
    case LinkClicked = 'link-clicked';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::CampaignCreated => 'info',
            self::RecipientCreated => 'info',
            self::RecipientImported => 'info',
            self::MailSent => 'success',
            self::SendingFailed => 'danger',
            self::LinkClicked => 'success',
        };
    }
}
