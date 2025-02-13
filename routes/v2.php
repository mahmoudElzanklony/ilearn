<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ActivationAccountController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\CategoriesControllerResource;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GeneralServiceController;
use App\Http\Controllers\SubjectsControllerResource;
use App\Http\Controllers\SubjectsVideosControllerResource;
use App\Http\Controllers\SubscriptionsControllerResource;
use App\Http\Controllers\VideoViewController;
use App\Http\Controllers\BillsControllerResource;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UniversitiesControllerResource;
use App\Http\Controllers\CacheSubjectVideosController;
use App\Http\Controllers\v2\SubjectsVideosController as v2SubjectsVideosController;
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


Route::name('v2.')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    Route::group(['middleware' => 'changeLang'], function () {
        // auth module
        Route::group(['prefix' => '/auth'], function () {
            Route::post('/login', [LoginController::class, 'login']);
            Route::post('/activate-account', [ActivationAccountController::class, 'index']);
            Route::post('/register', [RegisterController::class, 'register']);
            Route::post('/send-whatapp', [RegisterController::class, 'send_msg']);
            Route::post('/forget-password', [ForgetPasswordController::class, 'index']);
            Route::post('/new-password', [ForgetPasswordController::class, 'new_password']);
            Route::post('/logout', [RegisterController::class, 'logout']);
            Route::post('/me', [LoginController::class, 'get_user_by_token']);
            Route::post('/csrf', [LoginController::class, 'getToken']);
        });

        // get users
        Route::group(['prefix' => '/users', 'middleware' => 'auth:sanctum'], function () {
            Route::get('/', [UsersController::class, 'index']);
        });
        // get subjects for specific user
        Route::group(['prefix' => '/subjects-per-user', 'middleware' => 'auth:sanctum'], function () {
            Route::get('/', [SubjectsControllerResource::class, 'per_user']);
        });
        // notifications
        Route::group(['prefix' => '/notifications', 'middleware' => 'auth:sanctum'], function () {
            Route::get('/', [NotificationsController::class, 'index']);
            Route::post('/read-at', [NotificationsController::class, 'seen']);
        });
        // profile
        Route::group(['prefix' => '/profile', 'middleware' => 'auth:sanctum'], function () {
            Route::post('/update-info', [ProfileController::class, 'update_info']);
        });
        // video view
        Route::group(['prefix' => '/video-view', 'middleware' => 'auth:sanctum'], function () {
            Route::post('/seen', [VideoViewController::class, 'seen']);
            Route::get('/statics', [VideoViewController::class, 'statics']);
        });

        Route::post('/lock-subscription', [SubjectsControllerResource::class, 'lock']);
        Route::post('/total-money-subscriptions', [SubscriptionsControllerResource::class, 'total_money']);
        Route::group(['prefix' => '/stream-video'], function () {
            Route::get('/', [SubjectsVideosControllerResource::class, 'stream']);
            Route::get('/get-video-size', [SubjectsVideosControllerResource::class, 'get_size']);
            Route::get('/wasbi-generation', [SubjectsVideosControllerResource::class, 'wasbi_generation']);
        });


        Route::get('/cache-videos', CacheSubjectVideosController::class);


        // Define the remaining resource routes with middleware
        Route::group(['prefix' => '/bills-data', 'middleware' => 'auth:sanctum'], function () {
            Route::post('/check-period', [BillsControllerResource::class, 'check_period']);
        });

        // Define the remaining resource routes with middleware
        Route::group(['prefix' => '/versions', 'middleware' => 'auth:sanctum'], function () {
            Route::get('/', [\App\Http\Controllers\VersionsController::class, 'index']);
            Route::post('/update', [\App\Http\Controllers\VersionsController::class, 'update']);
        });

        // resources
        Route::resources([
            'universities' => UniversitiesControllerResource::class,
            'categories' => CategoriesControllerResource::class,
            'subjects' => SubjectsControllerResource::class,
            'subjects-videos' => v2SubjectsVideosController::class,
            'subscriptions' => SubscriptionsControllerResource::class,
            'bills' => BillsControllerResource::class,
        ]);


        Route::post('/deleteitem', [GeneralServiceController::class, 'delete_item']);

    });
});

