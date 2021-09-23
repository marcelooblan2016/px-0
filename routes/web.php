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
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ConvertController;

Route::get('/', [IndexController::class, 'index'])->name('home');

Route::get('/about', [IndexController::class, 'about'])->name('about');
Route::group(['prefix' => 'convert'], function () {
    Route::get('/', [ConvertController::class, 'index'])->name('convert.index');
    Route::post('/', [ConvertController::class, 'convertNow'])->name('convert.now');
});