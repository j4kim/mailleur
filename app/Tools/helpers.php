<?php

namespace App\Tools;

use Closure;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
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
    return "<div class=\"prose dark:prose-invert max-w-full\">$html</div>";
}

function renderProse(array $content)
{
    $rendered = RichContentRenderer::make($content)->toHtml();
    return prose($rendered);
}

function findNodeRecursive(array &$node, string $nodeType, Closure $callback)
{
    foreach ($node as $key => &$child) {
        if (gettype($child) !== 'array') {
            continue;
        }
        if (isset($child['type']) && $child['type'] === $nodeType) {
            $callback($child);
            continue;
        }
        $child = findNodeRecursive($child, $nodeType, $callback);
    }
    return $node;
}

function replaceMergeTags(array $content, array $mergeTags): array
{
    findNodeRecursive($content['content'], 'mergeTag', function (array &$node) use ($mergeTags) {
        $node['type'] = 'text';
        $node['text'] = $mergeTags[$node['attrs']['id']];
    });
    return $content;
}
