<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipient extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtolower($value),
        );
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getMergeTags(): array
    {
        $data = $this->data ?? [];
        return ['email' => $this->email, ...$data];
    }
}
