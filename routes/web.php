<?php

use App\Http\Controllers\API\CommandController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ServiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::post('/sendNotifications', [HomeController::class, 'sendNotifications'])->name('sendNotifications');
    Route::get('/export/reactions', [ExportController::class, 'getReactionsView'])->name('export.reactions.view');
    Route::post('/export/get_reactions', [ExportController::class, 'getReactionsForExport'])->name('export.get.reactions');
    Route::post('/export/reactions', [ExportController::class, 'exportReaction'])->name('export.reaction');
    Route::get('botan/staff/get', [HomeController::class, 'showStaff'])->name('showStaff');
    Route::get('/admin/command', [CommandController::class, 'command']);
    Route::get('/services', [ServiceController::class, 'show'])->name('service.show');
    Route::get('/service/create', [ServiceController::class, 'createView'])->name('service.createView');
    Route::post('/service/create', [ServiceController::class, 'create']);
    Route::get('/service/edit/{id}', [ServiceController::class, 'editView'])->name('service.edit');
    Route::post('/service/edit/{id}', [ServiceController::class, 'edit']);
    Route::get('/service/{id}/delete', [ServiceController::class, 'deleteService'])->name('service.delete');
    Route::get('/service/{serviceId}/topics', [ServiceController::class, 'topicList'])->name('service.topics');
    Route::get('/service/{serviceId}/topic/create', [ServiceController::class, 'createTopicView'])->name('service.topic.createView');
    Route::post('/service/{serviceId}/topic/create', [ServiceController::class, 'createTopic']);
    Route::get('/service/{serviceId}/topic/{topicId}', [ServiceController::class, 'editTopicView'])->name('service.topic.edit');
    Route::post('/service/{serviceId}/topic/{topicId}', [ServiceController::class, 'editTopic']);
    Route::get('/service/{serviceId}/topic/{topicId}/delete', [ServiceController::class, 'deleteTopic'])->name('service.topic.delete');
});