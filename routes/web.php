<?php

use App\Mail\CampaignMail;
use App\Models\Recipient;
use Illuminate\Support\Facades\Route;

Route::get('/mail/{recipient}/render', function (Recipient $recipient) {
    return new CampaignMail($recipient);
})->middleware('auth');
