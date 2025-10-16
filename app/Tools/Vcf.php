<?php

namespace App\Tools;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Vcf
{
    public string $vcf;
    public Collection $items;

    public function __construct(public string $filename)
    {
        $this->vcf = Storage::get($filename);
        $this->split();
    }

    public function split(): Collection
    {
        return $this->items = str($this->vcf)
            ->split('/\r?\n\r?\n/')
            ->map(fn(string $block) => self::blockToArray($block));
    }

    public static function blockToArray(string $block)
    {
        return str($block)->split('/\r?\n/')
            ->mapWithKeys(function (string $line) {
                $parts = explode(':', $line);
                if (count($parts) < 2) return [];
                return [$parts[0] => $parts[1]];
            });
    }
}
