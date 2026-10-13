<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FreelancerProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SkillController;


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

    // *****************
    Route::get('/skills', [
        SkillController::class,
        'index',
    ])->name('api.skills.index');
    
    Route::post('/skills', [
        SkillController::class,
        'store',
    ])->name('api.skills.store');
    
    Route::put('/skills/{skill}', [
        SkillController::class,
        'update',
    ])->name('api.skills.update');
    
    Route::delete('/skills/{skill}', [
        SkillController::class,
        'destroy',
    ])->name('api.skills.destroy');

    Route::get('/experiences', [
        \App\Http\Controllers\Api\ExperienceController::class,
        'index'
    ])->name('api.experiences.index');

       
    Route::post('/experiences', [
        \App\Http\Controllers\Api\ExperienceController::class,
        'store'
    ])->name('api.experiences.store');

    Route::put('/experiences/{experience}', [
        \App\Http\Controllers\Api\ExperienceController::class,
        'update'
    ])->name('api.experiences.update');
    
    Route::delete('/experiences/{experience}', [
        \App\Http\Controllers\Api\ExperienceController::class,
        'destroy'
    ])->name('api.experiences.destroy');

    

});