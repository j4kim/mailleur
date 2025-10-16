<?php

namespace App\Tools;

use Illuminate\Support\Collection;

class VcfContact
{
    private Collection $lines;
    public Collection $object;
    private Collection $keys;

    public ?string $email;

    function __construct(string $vcardBlock)
    {
        $this->lines = str($vcardBlock)
            ->split('/\r?\n/')
            ->filter(fn(string $line) => !empty($line));

        $this->object = $this->lines
            ->mapWithKeys(function (string $line) {
                [$k, $v] = explode(":", $line);
                return [$k => $v];
            })
            ->except(['BEGIN', 'VERSION', 'UID', 'PRODID', 'END', 'REV', 'item1.X-ABLABEL']);

        $this->keys = $this->object->keys();

        $this->email = $this->findEmail();
    }

    function findEmail(): ?string
    {
        $emailKey = $this->keys->first(fn(string $key) => str_contains($key, "EMAIL"));
        if (!$emailKey) return null;
        return $this->object->get($emailKey);
    }
}
