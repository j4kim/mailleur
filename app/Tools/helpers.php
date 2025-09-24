<?php

namespace App\Tools;

use Closure;
use Filament\Notifications\Notification;

function emailToName(string $email): string
{
    $name = str($email)->explode('@')->first();
    $name = str($name)->replace('.', ' ');
    $name = str($name)->headline()->toString();
    return $name;
}

function formatAddress(?array $addr): string
{
    if (!$addr || !$addr['address']) return "";
    if (!$addr['name']) return $addr['address'];
    return "$addr[name] <$addr[address]>";
}

function notif(string|Closure|null $message, string|Closure|null $title): Notification
{
    return Notification::make()->title($title)->body($message);
}

function successNotif(string $message, string $title = "Success"): Notification
{
    return notif($message, $title)->success()->send();
}

function errorNotif(string $message, string $title = "Error"): Notification
{
    return notif($message, $title)->danger()->send();
}

function prose(?string $html)
{
    return "<div class=\"prose dark:prose-invert\">$html</div>";
}
