<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
