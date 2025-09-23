<?php

namespace App\Tools;

function emailToName(string $email): string
{
    $name = str($email)->explode('@')->first();
    $name = str($name)->replace('.', ' ');
    $name = str($name)->headline()->toString();
    return $name;
}

function formatAddress(array $addr): string
{
    if (!$addr['address']) return "";
    if (!$addr['name']) return $addr['address'];
    return "$addr[name] <$addr[address]>";
}
