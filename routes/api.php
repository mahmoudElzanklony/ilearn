<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ActivationAccountController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\CategoriesControllerResource;
use App\Http\Controllers\ServicesControllerResource;
use App\Http\Controllers\PropertiesControllerResource;
use App\Http\Controllers\PropertiesHeadingControllerResource;
use App\Http\Controllers\CouponsControllerResource;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RatesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralServiceController;
use App\Http\Controllers\SubjectsControllerResource;
use App\Http\Controllers\SubjectsVideosControllerResource;
use App\Http\Controllers\SubscriptionsControllerResource;
use App\Http\Controllers\VideoViewController;
use App\Http\Controllers\BillsControllerResource;
use App\Http\Controllers\UsersController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware'=>'changeLang'],function (){
    // auth module
    Route::group(['prefix'=>'/auth'],function (){
        Route::post('/login',[LoginController::class,'login']);
        Route::post('/activate-account',[ActivationAccountController::class,'index']);
        Route::post('/register',[RegisterController::class,'register']);
        Route::post('/forget-password',[ForgetPasswordController::class,'index']);
        Route::post('/new-password',[ForgetPasswordController::class,'new_password']);
        Route::post('/logout',[LoginController::class,'logout']);
        Route::post('/me',[LoginController::class,'get_user_by_token']);
        Route::post('/csrf',[LoginController::class,'getToken']);
    });

    // get users
    Route::group(['prefix'=>'/users','middleware'=>'auth:sanctum'],function (){
        Route::get('/',[UsersController::class,'index']);
    });
    // get subjects for specific user
    Route::group(['prefix'=>'/subjects-per-user','middleware'=>'auth:sanctum'],function (){
        Route::get('/',[SubjectsControllerResource::class,'per_user']);
    });
    // notifications
    Route::group(['prefix'=>'/notifications','middleware'=>'auth:sanctum'],function (){
        Route::get('/',[NotificationsController::class,'index']);
        Route::post('/read-at',[NotificationsController::class,'seen']);
    });
    // profile
    Route::group(['prefix'=>'/profile','middleware'=>'auth:sanctum'],function (){
        Route::post('/update-info',[ProfileController::class,'update_info']);
    });
    // video view
    Route::group(['prefix'=>'/video-view','middleware'=>'auth:sanctum'],function (){
        Route::post('/seen',[VideoViewController::class,'seen']);
        Route::get('/statics',[VideoViewController::class,'statics']);
    });

    Route::post('/lock-subscription',[SubjectsControllerResource::class,'lock']);

    // Define the remaining resource routes with middleware
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::resource('bills', BillsControllerResource::class)->except('index');
    });
    // resources
    Route::resources([
        'categories'=>CategoriesControllerResource::class,
        'subjects'=>SubjectsControllerResource::class,
        'subjects-videos'=>SubjectsVideosControllerResource::class,
        'subscriptions'=>SubscriptionsControllerResource::class,
    ]);




    Route::post('/deleteitem',[GeneralServiceController::class,'delete_item']);

});

