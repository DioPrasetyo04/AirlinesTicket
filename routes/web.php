<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(HomeController::class)->group(function () {
    Route::get("/", "index")->name('home');
});

Route::controller(FlightController::class)->group(function () {
    Route::get('/flight', 'index')->name('flight.index');
    Route::get('/flight/{flightNumber}/choose-tier', 'show')->name('flight.show');
});

Route::controller(BookingController::class)->group(function () {
    Route::get('/check-booking', 'checkBooking')->name('booking.check');
    Route::get('flight/booking/{flightNumber}', 'booking')->name('booking');
    Route::get('flight/booking/{flightNumber}/choose-seat', 'chooseSeat')->name('booking.chooseSeat');
});
