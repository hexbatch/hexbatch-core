<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\PathControllerX;
use App\Http\Controllers\API\ElsewhereController;
use App\Http\Controllers\API\DesignControllerX;
use App\Http\Controllers\API\NamespaceControllerX;
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
        Route::post('/this', [ElsewhereController::class, 'me'])->name('core.servers.this');
    });



    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/me', [AuthenticationController::class, 'me'])->name('core.users.me');

            Route::delete('/logout', [AuthenticationController::class, 'logout'])->name('core.users.logout');

            Route::delete('/delete', [AuthenticationController::class, 'delete_user'])
                ->name('core.users.auth.delete');

            Route::prefix('auth')->group(function () {

                Route::post('/create/{seconds_to_live?}', [AuthenticationController::class, 'create_token'])
                    ->name('core.users.auth.create')->whereNumber('seconds_to_live');

                Route::get('/passthrough', [AuthenticationController::class, 'get_token_passthrough'])
                    ->name('core.users.auth.passthrough');

                Route::delete('/remove_current_token', [AuthenticationController::class, 'remove_current_token'])
                    ->name('core.users.auth.remove_current_token');
            });
        });


        Route::prefix('servers')->group(function () {
            Route::post('/list', [ElsewhereController::class, 'list_servers'])->name('core.servers.list');
            Route::get('/get/{server}', [ElsewhereController::class, 'show_server'])->name('core.servers.get');

            /*
             * part of elsewhere
             */
        });

        Route::prefix('namespaces')->group(function () {

            Route::post('/create/{?server}', [NamespaceControllerX::class, 'create_namespace'])->name('core.namespaces.create');

            Route::group(['prefix' => '{user_namespace}'], function () {
                Route::middleware(ValidateNamespaceOwner::class)->group( function () {
                    Route::post('/transfer/{user}', [NamespaceControllerX::class, 'transfer_namespace'])->name('core.namespaces.transfer');
                    Route::delete('/destroy', [NamespaceControllerX::class, 'destroy_namespace'])->name('core.namespaces.destroy');

                    Route::put('/admin/add/{target_namespace}', [NamespaceControllerX::class, 'group_admin_add'])->name('core.groups.admin.add');
                    Route::patch('/admin/remove/{target_namespace}', [NamespaceControllerX::class, 'group_admin_remove'])->name('core.groups.admin.remove');

                });

                Route::middleware(ValidateNamespaceAdmin::class)->group( function () {
                    Route::put('/member/add/{target_namespace}', [NamespaceControllerX::class, 'group_member_add'])->name('core.groups.member.add');
                    Route::delete('/member/remove/{target_namespace}', [NamespaceControllerX::class, 'group_member_remove'])->name('core.groups.member.remove');

                });

                Route::middleware(ValidateNamespaceMember::class)->group( function () {
                    Route::get('/my_namespaces', [NamespaceControllerX::class, 'list_my_namespaces'])->name('core.namespaces.my_namespaces');
                    Route::get('/get/{levels?}', [NamespaceControllerX::class, 'get_namespace'])->name('core.namespaces.get');
                    Route::get('/list_members/{levels?}', [NamespaceControllerX::class, 'list_members'])->name('core.groups.list_members');

                });
            });

        });


        Route::group(['prefix' => '{user_namespace}'], function () {

            Route::prefix('paths')->group(function () {
                Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                    Route::post('/create', [PathControllerX::class, 'create_path_x'])->name('core.paths.create');
                    Route::prefix('{path}')->group(function () {
                        Route::delete('/delete', [PathControllerX::class, 'delete_path_x'])->name('core.paths.delete');
                    });
                });

                Route::prefix('{path}')->middleware([ValidateNamespaceAdmin::class])->group(function () {
                    Route::patch('/update', [PathControllerX::class, 'update_path_x'])->name('core.paths.update');

                    Route::prefix('/{path_part}')->middleware(ValidatePartOwnership::class)->group(function () {
                        Route::patch('/edit_part', [PathControllerX::class, 'edit_part_x'])->name('core.paths.parts.edit');
                        Route::patch('/add_subtree', [PathControllerX::class, 'add_part_subtree_x'])->name('core.paths.parts.add_subtree');
                        Route::delete('/remove_subtree', [PathControllerX::class, 'delete_part_subtree_x'])->name('core.paths.parts.remove_subtree');
                    });
                });


                Route::middleware(ValidateNamespaceMember::class)->group(function (){
                    Route::get('/list', [PathControllerX::class, 'list_paths_x'])->name('core.paths.list');
                    Route::get('/test', [PathControllerX::class, 'path_test_x'])->name('core.paths.test');
                    Route::prefix('/{path_part}')->middleware(ValidatePartOwnership::class)->group(function () {
                        Route::get('/get/{levels?}', [PathControllerX::class, 'get_part_x'])->name('core.paths.get');
                    });

                });



            }); //end paths

            Route::prefix('design')->group(function () {


                Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                    Route::post('/create', [DesignControllerX::class, 'create_type'])->name('core.design.create');
                });//todo put in design guard for the edit stuff

                Route::prefix('/{element_type}')->middleware([ValidateTypeNotInUse::class])->group(function () {
                    Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                        Route::delete('/destroy', [DesignControllerX::class, 'destroy_type'])->name('core.design.destroy');
                    });

                    Route::middleware(ValidateNamespaceAdmin::class)->group(function () {
                        Route::patch('/edit', [DesignControllerX::class, 'edit_type'])->name('core.design.edit');
                    });
                });


                Route::middleware(ValidateNamespaceMember::class)->group(function (){
                    Route::get('/{element_type}/get/{levels?}', [DesignControllerX::class, 'get_type'])->name('core.design.get');
                    Route::get('/list', [DesignControllerX::class, 'list_types'])->name('core.design.list');
                    Route::get('/ping_map', [DesignControllerX::class, 'type_ping_map'])->name('core.design.ping_map');
                    Route::get('/ping_time', [DesignControllerX::class, 'type_ping_time'])->name('core.design.ping_time');
                });


                Route::group(['prefix' => '{element_type}/attributes'], function () {


                    Route::middleware([ValidateNamespaceAdmin::class,ValidateTypeNotInUse::class])->group(function (){
                        Route::post('/create', [DesignControllerX::class, 'new_attribute'])->name('core.design.attributes.create');

                        Route::prefix('{attribute}')->middleware(ValidateAttributeOwnership::class)->group(function () {
                            Route::patch('/edit', [DesignControllerX::class, 'edit_attribute'])->name('coretypes..attributes.edit');

                            Route::delete('/destroy', [DesignControllerX::class, 'delete_attribute'])->name('core.design.attributes.destroy');
                        });

                    });

                    Route::middleware(ValidateNamespaceMember::class)->group(function (){
                        Route::prefix('{attribute}')->middleware(ValidateAttributeOwnership::class)->group(function () {
                            Route::get('/get/{levels?}', [DesignControllerX::class, 'attribute_get'])->name('core.design.attributes.get');
                            Route::get('/ping_shape', [DesignControllerX::class, 'attribute_ping_shape'])->name('core.design.attributes.ping');
                        });

                        Route::get('/list/{filter?}', [DesignControllerX::class, 'attributes_list'])->name('core.design.attributes.list');
                    });



                    Route::prefix('{attribute}/rules')->middleware([ValidateAttributeOwnership::class,ValidateTypeNotInUse::class])->group(function () {

                        Route::middleware(ValidateNamespaceAdmin::class)->group(function (){
                            Route::post('/create', [DesignControllerX::class, 'create_rules'])->name('core.design.attributes.rules.create');
                            Route::patch('/update', [DesignControllerX::class, 'update_rules'])->name('core.design.attributes.rules.update');
                            Route::delete('/delete', [DesignControllerX::class, 'delete_rules'])->name('core.design.attributes.rules.delete');

                            Route::prefix('/{attribute_rule}')->middleware(ValidateRuleOwnership::class)->group(function () {
                                Route::patch('/edit_rule', [DesignControllerX::class, 'edit_rule'])->name('core.design.attributes.rules.edit_rule');
                                Route::patch('/add_subtree', [DesignControllerX::class, 'add_rule_subtree'])->name('core.design.attributes.rules.add_subtree');
                                Route::delete('/remove_subtree', [DesignControllerX::class, 'delete_rule_subtree'])->name('core.design.attributes.rules.remove_subtree');
                            });
                        });

                        Route::middleware(ValidateNamespaceMember::class)->group(function (){
                            Route::get('/list', [DesignControllerX::class, 'attribute_list_rules'])->name('core.design.attributes.rules.list');
                            Route::get('/test', [DesignControllerX::class, 'rule_test'])->name('core.design.attributes.rules.test');
                            Route::prefix('/{attribute_rule}')->middleware(ValidateRuleOwnership::class)->group(function () {
                                Route::get('/get/{levels?}', [DesignControllerX::class, 'attribute_get_rule'])->name('core.design.attributes.rules.get');
                            });

                        });
                    });
                }); //end attributes



            }); //end design



        }); //end user namespace defined behavior

/*


//todo routes to sets, elements, lives are prepended by the phase, which is the type name of the phase row

  .-.-.   .-.-.   .-.-.   .-.-.   .-.-.   .-.-.   .-.-.   .-.-
 / / \ \ / / \ \ / / \ \ / / \ \ / / \ \ / / \ \ / / \ \ / / \
`-'   `-`-'   `-`-'   `-`-'   `-`-'   `-`-'   `-`-'   `-`-'


 */


    }); //end auth protected



}); //end v1


