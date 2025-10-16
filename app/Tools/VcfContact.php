<?php

namespace App\Tools;

use Illuminate\Support\Collection;

class VcfContact
{
    private Collection $lines;
    private Collection $object;
    private Collection $keys;
    private array $emailParts;

    public ?string $email;
    public ?string $n;
    public ?string $fn;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $org;

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
        $this->emailParts = str($this->email)->explode('@')->toArray();

        $this->n = $this->object->get('N');
        $this->fn = $this->object->get('FN');
        $names = $this->guessNames();

        $this->first_name = $names[0];
        $this->last_name = $names[1];

        $this->org = $this->guessOrg();
    }

    function findEmail(): ?string
    {
        $emailKey = $this->keys->first(fn(string $key) => str_contains($key, "EMAIL"));
        if (!$emailKey) return null;
        return $this->object->get($emailKey);
    }

    function guessNames(): array
    {
        // N contains Last Name;First name;;;
        // or sometimes email@gmail.com;;;;
        // or sometimes empty
        $nValues = str($this->n)->explode(';')->filter();
        if ($nValues->count() > 1) {
            return [
                str_replace(ucfirst($nValues[1]), '\,', ''),
                ucfirst($nValues[0])
            ];
        }
        // if N contains 0 or 1 value, we guess from FN
        // FN contains full name or email
        if ($this->fn === $this->email) {
            return $this->guessNamesFromEmail();
        }
        if (str_contains($this->fn, " ")) {
            $fn = str($this->fn);
            return [
                ucfirst($fn->before(" ")->replace('\,', '')),
                ucfirst($fn->after(" ")),
            ];
        }
        return [ucfirst($this->fn), null];
    }

    function guessNamesFromEmail(): array
    {
        $beforeArobase = $this->emailParts[0];
        if (in_array(strtolower($beforeArobase), ['contact', 'info', 'admin'])) {
            return [
                $this->guessOrg(),
                null,
            ];
        }
        $parts = str($beforeArobase)->explode(".");
        return [
            ucfirst($parts[0]),
            ucfirst(@$parts[1] ?? ''),
        ];
    }

    function guessOrg(): ?string
    {
        if ($this->object->has('ORG')) {
            return $this->object['ORG'];
        }
        $afterArobase = $this->emailParts[1];
        $beforeDot = str($afterArobase)->before(".");
        if (in_array($beforeDot, ['hotmail', 'gmail', 'yahoo', 'googlemail', 'bluewin', 'outlook'])) {
            return null;
        }
        return ucfirst($beforeDot);
    }
}
