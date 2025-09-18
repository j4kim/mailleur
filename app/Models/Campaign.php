<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Campaign extends Model
{
    protected function casts(): array
    {
        return [
            'template' => 'array',
            'columns' => 'array',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }

    public function importCsv(string $filename)
    {
        $csv = Storage::get($filename);
        dd($csv);
    }
}
