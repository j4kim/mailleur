<?php

use App\Mail\CampaignMail;
use App\Models\Link;
use App\Models\Recipient;
use Illuminate\Support\Facades\Route;

Route::get('/mail/{recipient}/render', function (Recipient $recipient) {
    return new CampaignMail($recipient);
})->middleware('auth');

Route::get('redirect/{token}', function (string $token) {
    $link = Link::where('token', $token)->firstOrFail();
    return redirect($link->url);
});
