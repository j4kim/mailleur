<?php

namespace App\Tools;

function emailToName(string $email)
{
    $name = str($email)->explode('@')->first();
    $name = str($name)->replace('.', ' ');
    $name = str($name)->headline()->toString();
    return $name;
}
