<?php

use App\Mail\CampaignMail;
use App\Models\Recipient;
use Illuminate\Support\Facades\Route;

Route::get('/mail/{recipient}/render', function (Recipient $recipient) {
    if (!$recipient->mail_body) {
        $recipient->mail_body = $recipient->generateMailBody();
    }
    $recipient->rendered_mail_body = $recipient->renderMailBody();
    return new CampaignMail($recipient);
})->middleware('auth');
