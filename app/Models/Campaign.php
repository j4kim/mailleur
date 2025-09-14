<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
