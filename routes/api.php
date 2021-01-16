<?php

use App\Custom\ProfileFields;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\ContactUsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/auth_main', [UserAuthController::class, 'main']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/auth_dashboard', [UserAuthController::class, 'dashboard']); // user context frontend dashboard
    Route::get('/auth_photo', [UserAuthController::class, 'photo']); // used at frontend dashboard userProfile Photo
    Route::get('/auth_profile', [UserAuthController::class, 'profile']); // used at frontend dashboard userProfile
    Route::post('/auth_profile', [UserProfileController::class, 'store']); // used at frontend main - store userprofile
    Route::patch('/auth_profile', [UserProfileController::class, 'update']); // used at frontend dashboard - update userprofile

    Route::middleware('checkIfAdmin')->group( function() {

        Route::get('users/photo/{id}', [UserController::class, 'photo']);
        Route::apiResource('users', UserController::class)->except(['store',]);

    });

});

Route::get('/profileFields', function (ProfileFields $fields) {
    return collect($fields::getFields());
});

Route::post('/contact', ContactUsController::class);

Auth::routes();
