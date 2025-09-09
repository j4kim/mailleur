<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected function casts(): array
    {
        return [
            'body' => 'array',
        ];
    }
}
