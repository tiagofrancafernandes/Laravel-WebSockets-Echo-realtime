<?php

use App\Http\Controllers\StaticLoginController;
use App\Http\Controllers\StaticMessageController;
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

Route::get('/', [StaticLoginController::class, 'index'])->name('site_root');
StaticLoginController::routes();
StaticMessageController::routes();
