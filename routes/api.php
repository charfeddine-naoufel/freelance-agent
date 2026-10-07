<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FreelancerProfileController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post('/login', [
    AuthController::class,
    'login'
])->name('api.login');


/*
|--------------------------------------------------------------------------
| Routes protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ])->name('api.logout');

    Route::get('/me', [
        AuthController::class,
        'me'
    ])->name('api.me');


    Route::get('/profile', [
        FreelancerProfileController::class,
        'show'
    ])->name('api.profile.show');

    Route::put('/profile', [
        FreelancerProfileController::class,
        'update'
    ])->name('api.profile.update');

});