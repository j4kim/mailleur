<?php

use App\Mail\CampaignMail;
use App\Models\EventLog;
use App\Models\Recipient;
use Illuminate\Support\Facades\Route;

Route::get('/mail/{recipient}/render', function (Recipient $recipient) {
    return new CampaignMail($recipient);
})->middleware('auth');

Route::get('/event-logs/{eventLog}/sending-failed', function (EventLog $eventLog) {
    return view('event-logs.sending-failed', $eventLog);
})->middleware('auth')->name('event-logs.sending-failed');

Route::get('/event-logs/{eventLog}/mail-sent', function (EventLog $eventLog) {
    return view('event-logs.mail-sent', $eventLog);
})->middleware('auth')->name('event-logs.mail-sent');
