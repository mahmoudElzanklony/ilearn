<?php

use App\Http\Controllers\Employees\CreateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employees\IndexController;
use App\Http\Controllers\WebLoginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login-by-phone',WebLoginController::class);

Route::group(['prefix'=>'/employees'],function (){
   Route::get('/', IndexController::class);
   Route::post('/create', CreateController::class);
});

