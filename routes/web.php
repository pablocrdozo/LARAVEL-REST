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

Route::get('api/books/create/{isbn}', 'App\Http\Controllers\ServiceApiBooksController@create');
Route::resource('api/books', 'App\Http\Controllers\ApiBooksResourceController');
Route::get('api/books/delete/{isbn}', 'App\Http\Controllers\ServiceApiBooksController@delete');