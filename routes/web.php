<?php

use App\Http\Controllers\FlightController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(HomeController::class)->group(function () {
    Route::get("/", "index");
});

Route::controller(FlightController::class)->group(function () {
    Route::get('/flight', 'index')->name('flight.index');
});
