<?php

use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'midtrans-callback', [MidtransController::class, 'callback'])->name('midtrans.callback');
