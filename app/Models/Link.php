<?php

namespace App\Models;

use App\Enums\EventLogType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function logClick()
    {
        $this->recipient->logEvent(EventLogType::LinkClicked);
    }
}
