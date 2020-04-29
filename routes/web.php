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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/new', 'HomeController@newTicket');
Route::post('/home/new', 'HomeController@saveTicket');
Route::get('/home/reply/{id}', 'HomeController@newReply');
Route::post('/home/reply/{id}', 'HomeController@saveReply');
Route::get('/home/file/{id}', 'HomeController@getFile');
