<?php

namespace App\Tools;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use SplFileObject;

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

    public function toCsv(string $output)
    {
        $file = new SplFileObject($output, 'w');
        $header = collect(['email', 'first_name', 'last_name', 'org']);
        $file->fputcsv($header->toArray());
        foreach ($this->items as $vcfContact) {
            $file->fputcsv($header->map(fn($key) => $vcfContact->$key)->toArray());
        }
    }
}
