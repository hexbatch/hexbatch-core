<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\ElementController;
use App\Http\Controllers\API\LocationBoundController;
use App\Http\Controllers\API\TimeBoundController;
use App\Http\Controllers\API\UserGroupController;
use App\Http\Controllers\API\AttributeController;
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

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/me', [AuthenticationController::class, 'me']);

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


        Route::prefix('group')->group(function () {
            Route::post('/create/{group_name}', [UserGroupController::class, 'group_create'])->name('core.group.create');
            Route::delete('/{user_group}/destroy', [UserGroupController::class, 'group_destroy'])->name('core.group.destroy');
            Route::get('/{user_group}/list', [UserGroupController::class, 'group_list_members'])->name('core.group.list');
            Route::get('/{user_group}/get', [UserGroupController::class, 'group_get'])->name('core.group.get');
            Route::put('/{user_group}/member/add/{user}', [UserGroupController::class, 'group_member_add'])->name('core.group.member.add');
            Route::delete('/{user_group}/member/remove/{user}', [UserGroupController::class, 'group_member_remove'])->name('core.group.member.remove');
            Route::put('/{user_group}/admin/add/{user}', [UserGroupController::class, 'group_admin_add'])->name('core.group.admin.add');
            Route::patch('/{user_group}/admin/remove/{user}', [UserGroupController::class, 'group_admin_remove'])->name('core.group.admin.remove');
        });

        Route::prefix('bounds')->group(function () {
            Route::prefix('schedule')->group(function () {
                Route::get('/{time_bound}/get', [TimeBoundController::class, 'time_bound_get'])->name('core.bounds.schedule.get');
                Route::delete('/{time_bound}/delete', [TimeBoundController::class, 'time_bound_delete'])->name('core.bounds.schedule.delete');
                Route::patch('/{time_bound}/edit', [TimeBoundController::class, 'time_bound_edit'])->name('core.bounds.schedule.edit');
                Route::get('/{time_bound}/ping/{time_to_ping}', [TimeBoundController::class, 'time_bound_ping'])->name('core.bounds.schedule.ping');
                Route::post('/create', [TimeBoundController::class, 'time_bound_create'])->name('core.bounds.schedule.create');
                Route::get('/list/{user?}', [TimeBoundController::class, 'time_bound_list'])->name('core.bounds.schedule.list');
            });

            Route::prefix('location')->group(function () {
                Route::get('/{location_bound}/get', [LocationBoundController::class, 'location_bound_get'])->name('core.bounds.location.get');
                Route::delete('/{location_bound}/delete', [LocationBoundController::class, 'location_bound_delete'])->name('core.bounds.location.delete');
                Route::patch('/{location_bound}/edit', [LocationBoundController::class, 'location_bound_edit'])->name('core.bounds.location.edit');
                Route::get('/{location_bound}/ping/{location_json_to_ping}', [LocationBoundController::class, 'location_bound_ping'])->name('core.bounds.location.ping');
                Route::post('/create/{location_type}', [LocationBoundController::class, 'location_bound_create'])->name('core.bounds.location.create');
                Route::get('/list/{user?}', [LocationBoundController::class, 'location_bound_list'])->name('core.bounds.location.list');
            });
        }); //end bounds

        Route::prefix('attribute')->group(function () {
            Route::post('/create', [AttributeController::class, 'attribute_create'])->name('core.attribute.create');
            Route::patch('/{attribute}/edit', [AttributeController::class, 'attribute_edit_patch'])->name('core.attribute.edit');
            Route::delete('/{attribute}/destroy', [AttributeController::class, 'attribute_delete'])->name('core.attribute.destroy');
            Route::get('/{attribute}/get/{levels?}', [AttributeController::class, 'attribute_get'])->name('core.attribute.get');
            Route::get('/{attribute}/ping/{attribute_ping_type}', [AttributeController::class, 'attribute_ping'])->name('core.attribute.ping');
            Route::get('/list/managed', [AttributeController::class, 'attribute_list_managed'])->name('core.attribute.list.managed');
        });
    }); //end auth protected

}); //end v1


