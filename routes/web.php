<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

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
    if (App::environment('local')) {
        return File::get(public_path() . '/main/index.html');
    } else {
        return response(1);
    }
});

Route::middleware(['auth:sanctum', 'checkIfCompletedProfile'])->group(function() {

    // Route::domain('dashboard.' . env('APP_DOMAIN', 'localhost'))->group(function() {

        Route::get('/dashboard', function() {
            return File::get(public_path() . '/asset-dashboard/index.html');
        });

        Route::get('/dashboard/{path}', function() {
            return File::get(public_path() . '/asset-dashboard/index.html');
        });

    // });

});
