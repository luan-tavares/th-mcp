<?php

use App\Http\Controllers\Webhooks\ReadWebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post("/webhook/readme", ReadWebhookController::class)
    ->withoutMiddleware([VerifyCsrfToken::class]);
