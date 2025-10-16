<?php

use App\Tools\Vcf;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('vcf-to-csv {filename}', function (string $filename) {
    $this->info("Convert $filename to csv");
    $vcf = new Vcf($filename);
    $this->line($vcf->items->count() . " contacts found");
    $this->line("Here is the first:");
    $this->line($vcf->items->first()->toJson(JSON_PRETTY_PRINT));
});
