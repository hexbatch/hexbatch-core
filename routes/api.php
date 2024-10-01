<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\ElementController;
use App\Http\Controllers\API\ServerController;
use App\Http\Controllers\API\TypeController;
use App\Http\Controllers\API\NamespaceController;
use App\Http\Middleware\ValidateNamespaceAdmin;
use App\Http\Middleware\ValidateNamespaceMember;
use App\Http\Middleware\ValidateNamespaceOwner;
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


    Route::prefix('users')->group(function () {
        Route::post('/login', [AuthenticationController::class, 'login'])->name('core.users.login');
        Route::post('/register', [AuthenticationController::class, 'register'])->name('core.users.register');
        Route::get('/get/{user}', [ElementController::class, 'get_user'])->name('core.users.read');
        Route::get('/avialable', [AuthenticationController::class, 'available']);
    });

    Route::prefix('servers')->group(function () {
        Route::post('/this', [ServerController::class, 'me'])->name('core.servers.this');
        Route::post('/list', [ServerController::class, 'list_servers'])->name('core.servers.list');
        Route::get('/get/{server}', [ServerController::class, 'get_server'])->name('core.servers.get');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/me', [AuthenticationController::class, 'me']);

            Route::post('/logout', [AuthenticationController::class, 'logout'])->name('core.users.logout');
            Route::delete('/delete', [AuthenticationController::class, 'delete_user'])->name('core.users.logout');
            Route::delete('/purge', [AuthenticationController::class, 'purge_user'])->name('core.users.auth.purge');

            Route::prefix('auth')->group(function () {

                Route::post('/create/{seconds_to_live?}', [AuthenticationController::class, 'create_token'])
                    ->name('core.users.auth.create')->whereNumber('seconds_to_live');

                Route::get('/passthrough', [AuthenticationController::class, 'get_token_passthrough'])
                    ->name('core.users.auth.passthrough');

                Route::delete('/delete', [AuthenticationController::class, 'delete_user'])
                    ->name('core.users.auth.delete');

                Route::delete('/purge', [AuthenticationController::class, 'purge_user'])
                    ->name('core.users.auth.purge');

            });

            Route::get('/namespaces', [NamespaceController::class, 'list_my_namespaces'])->name('core.users.groups.list');
        });

        Route::prefix('namespaces')->group(function () {

            Route::post('/create', [NamespaceController::class, 'create_namespace'])->name('core.namespaces.create');

            Route::group(['prefix' => '{user_namespace}'], function () {
                Route::group([], function () {
                    Route::post('/transfer/{user}', [NamespaceController::class, 'transfer_namespace'])->name('core.namespaces.transfer');
                    Route::delete('/destroy', [NamespaceController::class, 'destroy_namespace'])->name('core.namespaces.destroy');
                    Route::delete('/purge', [NamespaceController::class, 'purge_namespace'])->name('core.namespaces.destroy');

                    Route::put('/admin/add/{user_namespace}', [NamespaceController::class, 'group_admin_add'])->name('core.groups.admin.add');
                    Route::patch('/admin/remove/{user_namespace}', [NamespaceController::class, 'group_admin_remove'])->name('core.groups.admin.remove');

                })->middleware(ValidateNamespaceOwner::class);

                Route::group([], function () {
                    Route::put('/member/add/{user_namespace}', [NamespaceController::class, 'group_member_add'])->name('core.groups.member.add');
                    Route::delete('/member/remove/{user_namespace}', [NamespaceController::class, 'group_member_remove'])->name('core.groups.member.remove');

                })->middleware(ValidateNamespaceAdmin::class);

                Route::group([], function () {
                    Route::get('/get', [NamespaceController::class, 'get_namespace'])->name('core.namespaces.get');
                    Route::get('/list_members', [NamespaceController::class, 'list_members'])->name('core.groups.list_members');

                })->middleware(ValidateNamespaceMember::class);
            });

        });


        Route::group(['prefix' => '{user_namespace}'], function () {
            Route::prefix('types')->group(function () {

                Route::post('/create', [TypeController::class, 'create_type'])->name('core.types.create');

                Route::group([],function (){
                    Route::delete('/{element_type}/destroy', [TypeController::class, 'destroy_type'])->name('core.types.destroy');
                })->middleware(ValidateNamespaceOwner::class);

                Route::group([],function (){
                    Route::patch('/{element_type}/edit', [TypeController::class, 'edit_type'])->name('core.types.edit');
                })->middleware(ValidateNamespaceAdmin::class);

                Route::group([],function (){
                    Route::get('/{element_type}/get/{levels?}', [TypeController::class, 'get_type'])->name('core.types.get');
                    Route::get('/list', [TypeController::class, 'list_types'])->name('core.types.list');
                    Route::get('/ping/{attribute_ping_type}', [TypeController::class, 'type_ping'])->name('core.types.list');
                })->middleware(ValidateNamespaceMember::class);


                Route::group(['prefix' => '{element_type}/attributes'], function () {


                    Route::group([],function (){
                        Route::post('/create', [TypeController::class, 'new_attribute'])->name('core.types.attributes.create');
                        Route::patch('/{attribute}/edit', [TypeController::class, 'edit_attribute'])->name('coretypes..attributes.edit');
                        Route::delete('/{attribute}/destroy', [TypeController::class, 'delete_attribute'])->name('core.types.attributes.destroy');
                    })->middleware(ValidateNamespaceAdmin::class);

                    Route::group([],function (){
                        Route::post('/{attribute}/copy', [TypeController::class, 'copy_attribute'])->name('core.types.attributes.copy');
                        Route::get('/{attribute}/get/{levels?}', [TypeController::class, 'attribute_get'])->name('core.types.attributes.get');
                        Route::get('/{attribute}/ping/{attribute_ping_type}', [TypeController::class, 'attribute_ping'])->name('core.types.attributes.ping');
                        Route::get('/list/{filter?}', [TypeController::class, 'attributes_list'])->name('core.types.attributes.list');
                    })->middleware(ValidateNamespaceMember::class);








                    Route::prefix('{attribute}/rules')->group(function () {

                        Route::group([],function (){
                            Route::get('/clear', [TypeController::class, 'attribute_clear_rules'])->name('core.types.attributes.rules.clear');
                            Route::post('/new', [TypeController::class, 'attribute_new_rule'])->name('core.types.attributes.rules.create');
                            Route::patch('/{attribute_rule}/edit', [TypeController::class, 'attribute_edit_rule'])->name('core.types.attributes.rules.edit');
                            Route::delete('/{attribute_rule}/destroy', [TypeController::class, 'attribute_delete_rule'])->name('core.types.attributes.rules.destroy');
                        })->middleware(ValidateNamespaceAdmin::class);

                        Route::group([],function (){
                            Route::get('/list/{filter?}', [TypeController::class, 'attribute_list_rules'])->name('core.types.attributes.rules.list');
                            Route::get('/{attribute_rule}/get/{levels?}', [TypeController::class, 'attribute_get_rule'])->name('core.types.attributes.rules.get');
                        })->middleware(ValidateNamespaceMember::class);
                    });
                });



        });
        });

        Route::prefix('standard')->group(function () {
            Route::get('/list/{filter}', [\App\Http\Controllers\API\StandardController::class, 'attribute_list_standard'])->name('core.standard.list');
        });


    }); //end auth protected



}); //end v1


