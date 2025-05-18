<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::post('/messages', [MessageController::class, 'store'])->name('api.messages.store');