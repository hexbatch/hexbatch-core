<?php

use App\Http\Controllers\API\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('/login', [AuthenticationController::class, 'login'])->name('core.user.login');
        Route::post('/register', [AuthenticationController::class, 'register'])->name('core.user.register');
    });

    Route::prefix('user')->middleware('auth:sanctum')->group(function () {
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        Route::post('/logout', [AuthenticationController::class, 'logout'])->name('core.user.logout');

        Route::prefix('auth')->group(function () {

            Route::post('/create/{seconds_to_live?}', [AuthenticationController::class, 'create_token'])
                ->name('core.user.auth.create')->whereNumber('seconds_to_live');

            Route::get('/passthrough', [AuthenticationController::class, 'get_token_passthrough'])
                ->name('core.user.auth.passthrough');

            Route::delete('/delete', [AuthenticationController::class, 'delete_this_token'])
                ->name('core.user.auth.delete');
        });
    });
});


