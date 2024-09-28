<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\ElementController;
use App\Http\Controllers\API\RemoteController;
use App\Http\Controllers\API\StackController;

use App\Http\Controllers\API\TypeController;
use App\Http\Controllers\API\UserGroupController;
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
    //todo add server info, at least version for now

    Route::prefix('users')->group(function () {
        Route::post('/login', [AuthenticationController::class, 'login'])->name('core.users.login');
        Route::post('/register', [AuthenticationController::class, 'register'])->name('core.users.register');
        Route::get('/get/{user}', [ElementController::class, 'get_user'])->name('core.users.read');
        //todo api (get) to check if username is valid and available
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/me', [AuthenticationController::class, 'me']);

            Route::post('/logout', [AuthenticationController::class, 'logout'])->name('core.users.logout');

            Route::prefix('auth')->group(function () {

                Route::post('/create/{seconds_to_live?}', [AuthenticationController::class, 'create_token'])
                    ->name('core.users.auth.create')->whereNumber('seconds_to_live');

                Route::get('/passthrough', [AuthenticationController::class, 'get_token_passthrough'])
                    ->name('core.users.auth.passthrough');

                Route::delete('/delete', [AuthenticationController::class, 'delete_this_token'])
                    ->name('core.users.auth.delete');

                //todo delete user (The user is deleted from this server, any ownership of type and elements and groups,
                //  if in use is set to the special user for deleted users, anything not in use is deleted
            });

            Route::get('/groups', [UserGroupController::class, 'list_my_groups'])->name('core.users.groups.list');
        });


        Route::prefix('groups')->group(function () {
            Route::post('/create', [UserGroupController::class, 'group_create'])->name('core.groups.create');
            Route::delete('/{user_group}/destroy', [UserGroupController::class, 'group_destroy'])->name('core.groups.destroy');
            Route::get('/{user_group}/list', [UserGroupController::class, 'group_list_members'])->name('core.groups.list');
            Route::get('/{user_group}/get', [UserGroupController::class, 'group_get'])->name('core.groups.get');
            Route::put('/{user_group}/member/add/{user}', [UserGroupController::class, 'group_member_add'])->name('core.groups.member.add');
            Route::delete('/{user_group}/member/remove/{user}', [UserGroupController::class, 'group_member_remove'])->name('core.groups.member.remove');
            Route::put('/{user_group}/admin/add/{user}', [UserGroupController::class, 'group_admin_add'])->name('core.groups.admin.add');
            Route::patch('/{user_group}/admin/remove/{user}', [UserGroupController::class, 'group_admin_remove'])->name('core.groups.admin.remove');
        });





        Route::prefix('types')->group(function () {
            Route::post('/create', [TypeController::class, 'create_type'])->name('core.types.create');
            Route::patch('/{element_type}/edit', [TypeController::class, 'edit_type'])->name('core.types.edit');
            Route::delete('/{element_type}/destroy', [TypeController::class, 'destroy_type'])->name('core.types.destroy');
            Route::get('/{element_type}/get/{levels?}', [TypeController::class, 'get_type'])->name('core.types.get');
            Route::get('/list', [TypeController::class, 'list_types'])->name('core.types.list');
            Route::get('/ping/{attribute_ping_type}', [TypeController::class, 'type_ping'])->name('core.types.list');

            Route::group(['prefix' => '{element_type}/attributes'], function () {
                Route::post('/create', [TypeController::class, 'new_attribute'])->name('core.types.attributes.create');
                Route::patch('/{attribute}/edit', [TypeController::class, 'edit_attribute'])->name('coretypes..attributes.edit');
                Route::post('/{attribute}/copy', [TypeController::class, 'copy_attribute'])->name('core.types.attributes.copy');
                Route::delete('/{attribute}/destroy', [TypeController::class, 'delete_attribute'])->name('core.types.attributes.destroy');

                Route::get('/{attribute}/get/{levels?}', [TypeController::class, 'attribute_get'])->name('core.types.attributes.get');
                Route::get('/{attribute}/ping/{attribute_ping_type}', [TypeController::class, 'attribute_ping'])->name('core.types.attributes.ping');
                Route::get('/list/{filter?}', [TypeController::class, 'attributes_list'])->name('core.types.attributes.list');

                Route::prefix('{attribute}/rules')->group(function () {
                    Route::get('/list/{filter?}', [TypeController::class, 'attribute_list_rules'])->name('core.types.attributes.rules.list');
                    Route::get('/clear', [TypeController::class, 'attribute_clear_rules'])->name('core.types.attributes.rules.clear');
                    Route::post('/new', [TypeController::class, 'attribute_new_rule'])->name('core.types.attributes.rules.create');
                    Route::patch('/{attribute_rule}/edit', [TypeController::class, 'attribute_edit_rule'])->name('core.types.attributes.rules.edit');
                    Route::delete('/{attribute_rule}/destroy', [TypeController::class, 'attribute_delete_rule'])->name('core.types.attributes.rules.destroy');
                    Route::get('/{attribute_rule}/get/{levels?}', [TypeController::class, 'attribute_get_rule'])->name('core.types.attributes.rules.get');
                });
            });



        });

        Route::prefix('standard')->group(function () {
            Route::get('/list/{filter}', [\App\Http\Controllers\API\StandardController::class, 'attribute_list_standard'])->name('core.standard.list');
        });

        Route::prefix('remotes')->group(function () {
            Route::post('/create', [RemoteController::class, 'remote_create'])->name('core.remotes.create');
            Route::patch('/{remote}/edit', [RemoteController::class, 'remote_edit_patch'])->name('core.remotes.edit');
            Route::delete('/{remote}/destroy', [RemoteController::class, 'remote_delete'])->name('core.remotes.destroy');
            Route::get('/{remote}/get/{levels?}', [RemoteController::class, 'remote_get'])->name('core.remotes.get');
            Route::post('/{remote}/test/{remote_stack?}', [RemoteController::class, 'remote_test'])->name('core.remotes.test');
            Route::get('/list', [RemoteController::class, 'remote_list'])->name('core.remotes.list');

        });

        Route::prefix('activities')->group(function () {
            Route::post('/create/{remote}', [RemoteController::class, 'create_activity'])->name('core.remotes.activity.create');
            Route::post('/{remote_activity}/restack/{remote_stack?}', [RemoteController::class, 'restack_activity'])->name('core.remotes.activity.restack');
            Route::post('/{remote_activity}/update', [RemoteController::class, 'update_activity'])->name('core.remotes.activity.update');
            Route::get('/list/{remote_activity_status_type?}', [RemoteController::class, 'list_activities'])->name('core.remotes.activity.list');
            Route::get('/{remote_activity}/get/{levels?}', [RemoteController::class, 'get_activity'])->name('core.remotes.activity.get');
        });

        Route::prefix('stacks')->group(function () {
            Route::post('/create/{remote_stack?}', [StackController::class, 'create_stack'])->name('core.stacks.create');
            Route::patch('/append/{remote_stack}/{parent_stack_ref?}', [StackController::class, 'append_stack'])->name('core.stacks.append');
            Route::get('/{remote_stack}/show', [StackController::class, 'show_stack'])->name('core.stacks.show');
            Route::get('/{remote_stack}/execute', [StackController::class, 'execute_stack'])->name('core.stacks.execute');
            Route::get('/list', [StackController::class, 'stack_list'])->name('core.stacks.list');
            Route::delete('/{remote_stack}/delete', [StackController::class, 'stack_delete'])->name('core.stacks.delete');
        });
    }); //end auth protected



}); //end v1


