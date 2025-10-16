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
            ->split('/\r?\n\r?\n(?=B)/')
            ->map(fn(string $block, $index) => self::blockToArray($block, $index));
    }

    public static function blockToArray(string $block): Collection
    {
        return str($block)->split('/\r?\n/')
            ->filter(fn(string $line) => !empty($line))
            ->mapWithKeys(function (string $line) {
                [$k, $v] = explode(":", $line);
                return [$k => $v];
            })
            ->except(['BEGIN', 'VERSION', 'UID', 'PRODID', 'END', 'REV', 'item1.X-ABLABEL']);
    }
}
