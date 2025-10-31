<?php

namespace App\Models;

use App\Enums\EventLogType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLog extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (EventLog $eventLog) {
            $eventLog->created_at = now();
        });
    }

    protected function casts(): array
    {
        return [
            'type' => EventLogType::class,
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }
}
