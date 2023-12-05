<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\ElementController;
use App\Http\Controllers\API\UserGroupController;
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
        Route::get('/get/{user}', [ElementController::class, 'get_user'])->name('core.user.read');
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

        Route::get('/groups', [UserGroupController::class, 'list_my_groups'])->name('core.user.groups.list');
    });


    Route::prefix('group')->middleware('auth:sanctum')->group(function () {
        Route::post('/create/{group_name}', [UserGroupController::class, 'group_create'])->name('core.group.create');
        Route::delete('/{user_group}/destroy', [UserGroupController::class, 'group_destroy'])->name('core.group.destroy');
        Route::get('/{user_group}/list', [UserGroupController::class, 'group_list_members'])->name('core.group.list');
        Route::put('/{user_group}/member/add/{user}', [UserGroupController::class, 'group_member_add'])->name('core.group.member.add');
        Route::delete('/{user_group}/member/remove/{user}', [UserGroupController::class, 'group_member_remove'])->name('core.group.member.remove');
        Route::put('/{user_group}/admin/add/{user}', [UserGroupController::class, 'group_admin_add'])->name('core.group.admin.add');
        Route::patch('/{user_group}/admin/remove/{user}', [UserGroupController::class, 'group_admin_remove'])->name('core.group.admin.remove');
    });
});


