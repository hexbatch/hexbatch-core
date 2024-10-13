<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\PathController;
use App\Http\Controllers\API\ServerController;
use App\Http\Controllers\API\TypeController;
use App\Http\Controllers\API\NamespaceController;
use App\Http\Middleware\ValidateAttributeOwnership;
use App\Http\Middleware\ValidateNamespaceAdmin;
use App\Http\Middleware\ValidateNamespaceMember;
use App\Http\Middleware\ValidateNamespaceOwner;
use App\Http\Middleware\ValidatePartOwnership;
use App\Http\Middleware\ValidateRuleOwnership;
use App\Http\Middleware\ValidateTypeNotInUse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {


    Route::prefix('users')->group(function () {
        Route::post('/login', [AuthenticationController::class, 'login'])->name('core.users.login');
        Route::post('/register', [AuthenticationController::class, 'register'])->name('core.users.register');
        Route::get('/avialable', [AuthenticationController::class, 'available']);
    });

    Route::prefix('servers')->group(function () {
        Route::post('/this', [ServerController::class, 'me'])->name('core.servers.this');
    });

    //todo add single route to receive incoming remotes, put the type guid, and the guid of the thing row in the route definition,
    // process only if remote_wait_pending for that thing with matching type


    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/me', [AuthenticationController::class, 'me']);

            Route::post('/logout', [AuthenticationController::class, 'logout'])->name('core.users.logout');

            Route::delete('/delete', [AuthenticationController::class, 'delete_user'])
                ->name('core.users.auth.delete');

            Route::prefix('auth')->group(function () {

                Route::post('/create/{seconds_to_live?}', [AuthenticationController::class, 'create_token'])
                    ->name('core.users.auth.create')->whereNumber('seconds_to_live');

                Route::get('/passthrough', [AuthenticationController::class, 'get_token_passthrough'])
                    ->name('core.users.auth.passthrough');

                Route::get('/remove_current_token', [AuthenticationController::class, 'remove_current_token'])
                    ->name('core.users.auth.remove_current_token');



            });


        });


        Route::prefix('servers')->group(function () {
            Route::post('/list', [ServerController::class, 'list_servers'])->name('core.servers.list');
            Route::get('/get/{server}', [ServerController::class, 'get_server'])->name('core.servers.get');
        });

        Route::prefix('namespaces')->group(function () {

            Route::post('/create/{?server}', [NamespaceController::class, 'create_namespace'])->name('core.namespaces.create');

            Route::group(['prefix' => '{user_namespace}'], function () {
                Route::middleware(ValidateNamespaceOwner::class)->group( function () {
                    Route::post('/transfer/{user}', [NamespaceController::class, 'transfer_namespace'])->name('core.namespaces.transfer');
                    Route::delete('/destroy', [NamespaceController::class, 'destroy_namespace'])->name('core.namespaces.destroy');

                    Route::put('/admin/add/{user_namespace}', [NamespaceController::class, 'group_admin_add'])->name('core.groups.admin.add');
                    Route::patch('/admin/remove/{user_namespace}', [NamespaceController::class, 'group_admin_remove'])->name('core.groups.admin.remove');

                });

                Route::middleware(ValidateNamespaceAdmin::class)->group( function () {
                    Route::put('/member/add/{user_namespace}', [NamespaceController::class, 'group_member_add'])->name('core.groups.member.add');
                    Route::delete('/member/remove/{user_namespace}', [NamespaceController::class, 'group_member_remove'])->name('core.groups.member.remove');

                });

                Route::middleware(ValidateNamespaceMember::class)->group( function () {
                    Route::get('/my_namespaces', [NamespaceController::class, 'list_my_namespaces'])->name('core.namespaces.my_namespaces');
                    Route::get('/get/{levels?}', [NamespaceController::class, 'get_namespace'])->name('core.namespaces.get');
                    Route::get('/list_members/{levels?}', [NamespaceController::class, 'list_members'])->name('core.groups.list_members');

                });
            });

        });


        Route::group(['prefix' => '{user_namespace}'], function () {

            Route::prefix('paths')->group(function () {
                Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                    Route::post('/create', [PathController::class, 'create_path'])->name('core.paths.create');
                    Route::prefix('{path}')->group(function () {
                        Route::delete('/delete', [PathController::class, 'delete_path'])->name('core.paths.delete');
                    });
                });

                Route::prefix('{path}')->middleware([ValidateNamespaceAdmin::class])->group(function () {
                    Route::patch('/update', [PathController::class, 'update_path'])->name('core.paths.update');

                    Route::prefix('/{path_part}')->middleware(ValidatePartOwnership::class)->group(function () {
                        Route::patch('/edit_part', [PathController::class, 'edit_part'])->name('core.types.attributes.rules.edit_rule');
                        Route::patch('/add_subtree', [PathController::class, 'add_part_subtree'])->name('core.types.attributes.rules.add_subtree');
                        Route::delete('/remove_subtree', [PathController::class, 'delete_part_subtree'])->name('core.types.attributes.rules.remove_subtree');
                    });
                });


                Route::middleware(ValidateNamespaceMember::class)->group(function (){
                    Route::get('/list', [PathController::class, 'list_paths'])->name('core.types.attributes.rules.list');
                    Route::get('/test', [PathController::class, 'path_test'])->name('core.types.attributes.rules.test');
                    Route::prefix('/{path_part}')->middleware(ValidatePartOwnership::class)->group(function () {
                        Route::get('/get/{levels?}', [PathController::class, 'get_part'])->name('core.types.attributes.rules.get');
                    });

                });

            });

            Route::prefix('types')->group(function () {


                Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                    Route::post('/create', [TypeController::class, 'create_type'])->name('core.types.create');
                });//todo add parent, remove parent, and put in design guard for the edit stuff

                Route::prefix('/{element_type}')->middleware([ValidateTypeNotInUse::class])->group(function () {
                    Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                        Route::delete('/destroy', [TypeController::class, 'destroy_type'])->name('core.types.destroy');
                    });

                    Route::middleware(ValidateNamespaceAdmin::class)->group(function () {
                        Route::patch('/edit', [TypeController::class, 'edit_type'])->name('core.types.edit');
                    });
                });


                Route::middleware(ValidateNamespaceMember::class)->group(function (){
                    Route::get('/{element_type}/get/{levels?}', [TypeController::class, 'get_type'])->name('core.types.get');
                    Route::get('/list', [TypeController::class, 'list_types'])->name('core.types.list');
                    Route::get('/ping_map', [TypeController::class, 'type_ping_map'])->name('core.types.ping_map');
                    Route::get('/ping_time', [TypeController::class, 'type_ping_time'])->name('core.types.ping_time');
                });


                Route::group(['prefix' => '{element_type}/attributes'], function () {


                    Route::middleware([ValidateNamespaceAdmin::class,ValidateTypeNotInUse::class])->group(function (){
                        Route::post('/create', [TypeController::class, 'new_attribute'])->name('core.types.attributes.create');

                        Route::prefix('{attribute}')->middleware(ValidateAttributeOwnership::class)->group(function () {
                            Route::patch('/edit', [TypeController::class, 'edit_attribute'])->name('coretypes..attributes.edit');
                            Route::delete('/destroy', [TypeController::class, 'delete_attribute'])->name('core.types.attributes.destroy');
                        });

                    });

                    Route::middleware(ValidateNamespaceMember::class)->group(function (){
                        Route::prefix('{attribute}')->middleware(ValidateAttributeOwnership::class)->group(function () {
                            Route::get('/get/{levels?}', [TypeController::class, 'attribute_get'])->name('core.types.attributes.get');
                            Route::get('/ping_shape', [TypeController::class, 'attribute_ping_shape'])->name('core.types.attributes.ping');
                        });

                        Route::get('/list/{filter?}', [TypeController::class, 'attributes_list'])->name('core.types.attributes.list');
                    });



                    Route::prefix('{attribute}/rules')->middleware([ValidateAttributeOwnership::class,ValidateTypeNotInUse::class])->group(function () {

                        Route::middleware(ValidateNamespaceAdmin::class)->group(function (){
                            Route::post('/create', [TypeController::class, 'create_rules'])->name('core.types.attributes.rules.create');
                            Route::patch('/update', [TypeController::class, 'update_rules'])->name('core.types.attributes.rules.update');
                            Route::delete('/delete', [TypeController::class, 'delete_rules'])->name('core.types.attributes.rules.delete');

                            Route::prefix('/{attribute_rule}')->middleware(ValidateRuleOwnership::class)->group(function () {
                                Route::patch('/edit_rule', [TypeController::class, 'edit_rule'])->name('core.types.attributes.rules.edit_rule');
                                Route::patch('/add_subtree', [TypeController::class, 'add_rule_subtree'])->name('core.types.attributes.rules.add_subtree');
                                Route::delete('/remove_subtree', [TypeController::class, 'delete_rule_subtree'])->name('core.types.attributes.rules.remove_subtree');
                            });
                        });

                        Route::middleware(ValidateNamespaceMember::class)->group(function (){
                            Route::get('/list', [TypeController::class, 'attribute_list_rules'])->name('core.types.attributes.rules.list');
                            Route::get('/test', [TypeController::class, 'rule_test'])->name('core.types.attributes.rules.test');
                            Route::prefix('/{attribute_rule}')->middleware(ValidateRuleOwnership::class)->group(function () {
                                Route::get('/get/{levels?}', [TypeController::class, 'attribute_get_rule'])->name('core.types.attributes.rules.get');
                            });

                        });
                    });
                });



            });
        });




    }); //end auth protected



}); //end v1


