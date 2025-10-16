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
            ->mapInto(VcfContact::class);
    }
}
