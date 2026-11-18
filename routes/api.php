<?php

use App\Http\Controllers\Api\QrisWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/qris', QrisWebhookController::class)->name('webhook.qris');
