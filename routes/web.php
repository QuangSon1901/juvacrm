<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\Logs\Activity\ActivityController;
use App\Http\Controllers\Dashboard\Overview\OverviewController;
use App\Http\Controllers\Dashboard\Profile\ProfileController;
use App\Http\Controllers\Dashboard\Setting\SettingController;
use Illuminate\Support\Facades\Route;

/************************************************** Group Auth **************************************************/
Route::group(
    ['namespace' => 'Auth', 'as' => 'auth.', 'middleware' => []],
    function () {
        Route::get('/login', [LoginController::class, "index"])->name("login");
    }
);

/************************************************** Group Dashboard **************************************************/
Route::group(
    ['namespace' => 'Dashboard', 'as' => 'dashboard.', 'middleware' => []],
    function () {
        
        
        Route::group(
            ['namespace' => 'Overview', 'as' => 'overview.', 'middleware' => []],
            function () {
                Route::get('/', [OverviewController::class, "index"])->name("overview");
            }
        );

        Route::group(
            ['namespace' => 'Profile', 'as' => 'profile.', 'middleware' => []],
            function () {
                Route::get('/profile', [ProfileController::class, "index"])->name("profile");
            }
        );

        Route::group(
            ['namespace' => 'Logs', 'as' => 'logs.', 'middleware' => []],
            function () {
                
                Route::group(
                    ['namespace' => 'Activity', 'as' => 'activity.', 'middleware' => []],
                    function () {
                        Route::get('/activity', [ActivityController::class, "index"])->name("activity");
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Setting', 'as' => 'setting.', 'middleware' => []],
            function () {
                Route::get('/setting', [SettingController::class, "index"])->name("setting");
            }
        );
    }
);
