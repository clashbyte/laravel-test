<?php

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

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();



Route::middleware('auth')->group(function () {

    // Общие маршруты
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/home/file/{ticketID}', 'HomeController@getFile')->where('ticketID', '[0-9]+');

    // Маршруты клиента
    Route::middleware(\App\Http\Middleware\ClientAccess::class)->prefix('/home')->group(function () {
        Route::get('/new', 'ClientController@ticketForm');
        Route::post('/new', 'ClientController@saveTicket');
    });

    // Маршруты менеджера
    Route::middleware(\App\Http\Middleware\ManagerAccess::class)->prefix('/home')->group(function () {
        Route::get('/reply/{ticketID}', 'ManagerController@replyForm')->where('ticketID', '[0-9]+');
        Route::post('/reply', 'ManagerController@saveReply');
    });
});




