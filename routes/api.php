<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\TelegramController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['telegram.token']], function () {
    Route::post('start', [TelegramController::class, 'start']);
    Route::post('message', [TelegramController::class, 'message']);
    Route::post('callback', [TelegramController::class, 'callback']);
});



// Route::post('booking/showWeek', [BookingController::class, 'showWeek']);
// Route::post('booking/validateCalendar', [BookingController::class, 'validateCalendar']);
// Route::post('booking/validateTimePicker', [BookingController::class, 'validateTimePicker']);
// Route::post('booking/confirmDeleteBooking', [BookingController::class, 'confirmDeleteBooking']);
// Route::post('booking/deleteBooking', [BookingController::class, 'deleteBooking']);
// Route::post('saveReaction', [SettingController::class, 'saveReaction']);
