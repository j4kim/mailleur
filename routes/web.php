<?php

use App\Mail\CampaignMail;
use App\Models\Link;
use App\Models\EventLog;
use App\Models\Recipient;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

Route::get('/mail/{recipient}/render', function (Recipient $recipient) {
    if (!$recipient->mail_body) {
        $recipient->mail_body = $recipient->generateMailBody();
    }
    $recipient->rendered_mail_body = $recipient->renderMailBody();
    return new CampaignMail($recipient);
})->middleware('auth');

Route::get('redirect/{token}', function (string $token) {
    $link = Link::where('token', $token)->firstOrFail();
    $link->logClick();
    return redirect($link->url);
})->name('link-redirect');

Route::get('/event-logs/{eventLog}', function (EventLog $eventLog) {
    $view = "event-logs.{$eventLog->type->value}";
    return View::exists($view) ? View::make($view, $eventLog) : $eventLog;
})->middleware('auth')->name('event-log-details');
