<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

// Auth::routes();

Route::get('/add-workshop', 'WorkshopController@show')->name('add-workshop');
Route::post('/add-workshop', 'WorkshopController@store');
Route::post('/get-workshop', 'WorkshopController@get');
Route::post('/edit-workshop/{id}', 'WorkshopController@update');
Route::delete('/delete-workshop/{id}', 'WorkshopController@delete');

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
