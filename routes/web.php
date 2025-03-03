<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerconaController;
use App\Http\Controllers\UsageController;
use App\Http\Middleware\ExtendSession;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware([ExtendSession::class])->group(function () {

    Route::middleware(['auth'])->group(function () {

        Route::get('/', function () {
            return view('home');
        })->name('home');

        Route::controller(UsageController::class)->group(function () {
            Route::get('/usage', 'show')->name('usage.show');
        });

        Route::controller(PerconaController::class)->group(function () {
            Route::get('/percona', 'show')->name('percona.show');
            Route::post('/percona', 'show')->name('percona.showWithCommands');
            //Route::post('/percona/generateCommands', 'generateCommands')->name('percona.generateCommands');
            Route::post('/percona/run', 'runCommands')->name('percona.run');
        });

        Route::get('/config', function () {
            return 'WIP';
        })->name('config.show');

    });

});
