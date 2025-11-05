<?php

use App\Http\Controllers\Api;
use App\Http\Middleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::prefix('v1')->group(function () {


    Route::prefix('servers')->group(function () {
        Route::get('us', [Api\ServerController::class, 'us'])->name('core.servers.us');
    });

    Route::prefix('elements')->group(function () {
        Route::get('public', [Api\ElementController::class, 'show_public'])->name('core.elements.show_public');
    });

    Route::prefix('namespaces')->group(function () {
        Route::get('public/{user_namespace}', [Api\NamespaceController::class, 'show_namespace_public'])->name('core.namespaces.show_public');
    });

    Route::prefix('sets')->group(function () {
        Route::get('public', [Api\SetController::class, 'show_public'])->name('core.sets.show_public');
    });

    Route::prefix('types')->group(function () {
        Route::get('public/{element_type}', [Api\TypeController::class, 'show_type_public'])->name('core.types.show_public');
        Route::get('suspended', [Api\TypeController::class, 'list_all_suspended'])->name('core.types.list_all_suspended');
    });

    Route::prefix('elsewhere')->group(function () {
        Route::post('ask_credentials', [Api\ElsewhereController::class, 'ask_credentials'])->name('core.elsewhere.ask_credentials');
        Route::post('register', [Api\ElsewhereController::class, 'register_elsewhere'])->name('core.elsewhere.register');

        Route::get('list_servers', [Api\ElsewhereController::class, 'list_servers'])->name('core.elsewhere.list_servers');

        Route::prefix('{server}')->group(function () {
            Route::get('show', [Api\ElsewhereController::class, 'show_elsewhere_public'])->name('core.elsewhere.show');
        });
    });

    Route::prefix('users')->group(function () {
        Route::post('login', [Api\AuthenticationController::class, 'login'])->name('core.users.login');
        Route::post('register', [Api\AuthenticationController::class, 'register'])->name('core.users.register');
        Route::get('avialable', [Api\AuthenticationController::class, 'available'])->name('core.users.available');
    });





    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/me', [Api\AuthenticationController::class, 'me'])->name('core.users.me');

            Route::delete('/logout', [Api\AuthenticationController::class, 'logout'])->name('core.users.logout');



            Route::prefix('auth')->group(function () {

                Route::post('/create/{seconds_to_live?}', [Api\AuthenticationController::class, 'create_token'])
                    ->name('core.users.auth.create')->whereNumber('seconds_to_live');

                Route::get('/passthrough', [Api\AuthenticationController::class, 'get_token_passthrough'])
                    ->name('core.users.auth.passthrough');

                Route::delete('/remove_current_token', [Api\AuthenticationController::class, 'remove_current_token'])
                    ->name('core.users.auth.remove_current_token');

                Route::delete('/start_deletion', [Api\AuthenticationController::class, 'start_user_deletion'])
                    ->name('core.users.auth.start_deletion');

                Route::post('/prepare_deletion', [Api\AuthenticationController::class, 'prepare_user_deletion'])
                    ->name('core.users.auth.prepare_deletion');
            });
        });


        //todo this top level member check gets in the way of rules for reading and writing elements and types?
        Route::middleware(Middleware\ValidateNamespaceMember::class)->prefix('{user_namespace}')->group( function () {

            Route::prefix('server')->group(function () {
                Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group( function () {
                    Route::post('/edit', [Api\ServerController::class, 'edit_server'])->name('core.server.edit');
                    Route::get('/admin', [Api\ServerController::class, 'show_admin'])->name('core.server.admin');
                });
            });


            Route::prefix('operations')->group(function () {

                Route::post('unshift', [Api\OperationController::class, 'op_combine'])->name('core.operations.unshift');
                Route::post('combine', [Api\OperationController::class, 'op_unshift'])->name('core.operations.combine');
                Route::post('shift', [Api\OperationController::class, 'op_shift'])->name('core.operations.shift');
                Route::post('push', [Api\OperationController::class, 'op_push'])->name('core.operations.push');
                Route::post('pop', [Api\OperationController::class, 'op_pop'])->name('core.operations.pop');
                Route::post('mutual', [Api\OperationController::class, 'op_mutual'])->name('core.operations.mutual');
            });


            Route::prefix('elsewhere')->group(function () {

                Route::prefix('{server}')->group(function () {

                    Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group( function () {
                        Route::post('push_credentials', [Api\ElsewhereController::class, 'push_credentials'])->name('core.elsewhere.push_credentials');
                        Route::patch('change_status', [Api\ElsewhereController::class, 'change_status'])->name('core.elsewhere.change_status');
                        Route::delete('purge', [Api\ElsewhereController::class, 'purge_elsewhere'])->name('core.elsewhere.purge_elsewhere');

                        Route::post('ask_element', [Api\ElsewhereController::class, 'ask_element'])->name('core.elsewhere.ask_element');
                        Route::post('ask_type', [Api\ElsewhereController::class, 'ask_type'])->name('core.elsewhere.ask_type');
                        Route::post('ask_set', [Api\ElsewhereController::class, 'ask_set'])->name('core.elsewhere.ask_set');
                        Route::post('ask_namespace', [Api\ElsewhereController::class, 'ask_namespace'])->name('core.elsewhere.ask_namespace');

                        Route::post('push_element', [Api\ElsewhereController::class, 'push_element'])->name('core.elsewhere.push_element');
                        Route::post('push_set', [Api\ElsewhereController::class, 'push_set'])->name('core.elsewhere.push_set');
                        Route::post('push_namespace', [Api\ElsewhereController::class, 'push_namespace'])->name('core.elsewhere.push_namespace');
                        Route::post('push_type', [Api\ElsewhereController::class, 'push_type'])->name('core.elsewhere.push_type');
                        Route::post('push_event', [Api\ElsewhereController::class, 'push_event'])->name('core.elsewhere.push_event');
                        Route::get('show_admin', [Api\ElsewhereController::class, 'show_admin_elsewhere'])->name('core.elsewhere.admin_elsewhere');
                    });

                    Route::middleware(Middleware\ValidateElsewhereOwner::class)->group( function () {
                        Route::post('give_credentials', [Api\ElsewhereController::class, 'give_credentials'])->name('core.elsewhere.give_credentials');
                        Route::post('give_namespace', [Api\ElsewhereController::class, 'give_namespace'])->name('core.elsewhere.give_namespace');
                        Route::post('give_set', [Api\ElsewhereController::class, 'give_set'])->name('core.elsewhere.give_set');
                        Route::post('give_type', [Api\ElsewhereController::class, 'give_type'])->name('core.elsewhere.give_type');
                        Route::post('give_event', [Api\ElsewhereController::class, 'give_event'])->name('core.elsewhere.give_event');
                        Route::post('give_element', [Api\ElsewhereController::class, 'give_element'])->name('core.elsewhere.give_element');
                        Route::post('share_element', [Api\ElsewhereController::class, 'share_element'])->name('core.elsewhere.share_element');

                        Route::post('destroyed_element', [Api\ElsewhereController::class, 'destroyed_element'])->name('core.elsewhere.destroyed_element');
                        Route::post('suspended_type', [Api\ElsewhereController::class, 'suspended_type'])->name('core.elsewhere.suspended_type');
                    });
                });

            });



            Route::prefix('namespaces')->group(function () {


                Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group(function () {
                    Route::post('promote', [Api\NamespaceController::class, 'promote_namespace'])->name('core.namespaces.promote');
                });

                Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                    Route::post('create', [Api\NamespaceController::class, 'create_namespace'])->name('core.namespaces.create');
                });

                Route::get('list', [Api\NamespaceController::class, 'list_namespaces'])->name('core.namespaces.list_namespaces');


                Route::prefix('{target_namespace}')->group(function () {

                    Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group(function () {
                        Route::patch('edit_promotion', [Api\NamespaceController::class, 'edit_promotion'])->name('core.namespaces.edit_promotion');
                        Route::delete('purge', [Api\NamespaceController::class, 'purge_namespace'])->name('core.namespaces.purge');
                        Route::delete('purge_admin', [Api\NamespaceController::class, 'purge_admin'])->name('core.namespaces.purge_admin');
                        Route::delete('purge_member', [Api\NamespaceController::class, 'purge_member'])->name('core.namespaces.purge_member');
                        Route::post('promote_admin', [Api\NamespaceController::class, 'promote_admin'])->name('core.namespaces.promote_admin');
                        Route::post('promote_member', [Api\NamespaceController::class, 'promote_member'])->name('core.namespaces.promote_member');

                    });


                    Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                        Route::post('start_transfer', [Api\NamespaceController::class, 'start_transfer'])->name('core.namespaces.start_transfer');
                        Route::post('transfer_owner', [Api\NamespaceController::class, 'transfer_owner'])->name('core.namespaces.transfer_owner');
                        Route::post('add_admin', [Api\NamespaceController::class, 'add_admin'])->name('core.namespaces.add_admin');
                        Route::delete('remove_admin', [Api\NamespaceController::class, 'remove_admin'])->name('core.namespaces.remove_admin');
                        Route::delete('destroy', [Api\NamespaceController::class, 'destroy_namespace'])->name('core.namespaces.destroy');

                    });


                    Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group(function () {
                        Route::patch('add_handle', [Api\NamespaceController::class, 'add_handle'])->name('core.namespaces.add_handle');
                        Route::patch('remove_handle', [Api\NamespaceController::class, 'remove_handle'])->name('core.namespaces.remove_handle');
                        Route::post('add_member', [Api\NamespaceController::class, 'add_member'])->name('core.namespaces.add_member');
                        Route::delete('remove_member', [Api\NamespaceController::class, 'remove_member'])->name('core.namespaces.remove_member');

                    });


                    Route::get('show', [Api\NamespaceController::class, 'show_namespace'])->name('core.namespaces.show');
                    Route::get('list_admins', [Api\NamespaceController::class, 'list_admins'])->name('core.namespaces.list_admins');
                    Route::get('list_members', [Api\NamespaceController::class, 'list_members'])->name('core.namespaces.list_members');//will was here

                });
            });


            Route::prefix('paths')->group(function () {

                Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                    Route::post('create', [Api\PathController::class, 'create_path'])->name('core.paths.create');
                });

                Route::middleware([])->group(function () {
                    Route::get('list', [Api\PathController::class, 'list_paths'])->name('core.paths.list');
                });


                Route::prefix('{path}')->group(function () {
                    Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                        Route::patch('publish', [Api\PathController::class, 'publish_path'])->name('core.paths.publish');
                        Route::delete('destroy', [Api\PathController::class, 'destroy_path'])->name('core.paths.destroy');
                    });


                    Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group(function () {
                        Route::patch('add_handle', [Api\PathController::class, 'add_handle'])->name('core.paths.add_handle');
                        Route::patch('remove_handle', [Api\PathController::class, 'remove_handle'])->name('core.paths.remove_handle');
                        Route::post('edit', [Api\PathController::class, 'edit_path'])->name('core.paths.edit');
                        Route::post('create_part', [Api\PathController::class, 'create_part'])->name('core.paths.create_part');

                        Route::prefix('part')->group(function () {
                            Route::prefix('{path_part}')->group(function () {
                                Route::middleware(Middleware\ValidatePartOwnership::class)->group(function () {
                                    Route::delete('destroy', [Api\PathController::class, 'destroy_part'])->name('core.paths.destroy_part');
                                    Route::patch('edit', [Api\PathController::class, 'edit_part'])->name('core.paths.edit_part');
                                });
                            });
                        });


                    });

                    Route::middleware([])->group(function () {
                        Route::get('search', [Api\PathController::class, 'search'])->name('core.paths.show');
                        Route::get('show', [Api\PathController::class, 'show_path'])->name('core.paths.show');
                        Route::get('copy', [Api\PathController::class, 'copy_path'])->name('core.paths.copy');

                        Route::prefix('part')->group(function () {
                            Route::prefix('{path_part}')->group(function () {
                                Route::middleware(Middleware\ValidatePartOwnership::class)->group(function () {
                                    Route::get('show', [Api\PathController::class, 'show_part_tree'])->name('core.paths.show_part');
                                    Route::get('test', [Api\PathController::class, 'test_part'])->name('core.paths.test_part');
                                });
                            });
                        });

                    });
                });


            }); //end paths


            Route::prefix('links')->group(function () {

                Route::prefix('phase/{working_phase}')->group(function () {
                    Route::get('list', [Api\LinkController::class, 'list_links'])->name('core.links.list');

                    Route::prefix('link')->group(function () {
                        Route::prefix('{element_link}')->group(function () {
                            Route::middleware(Middleware\ValidatePhaseOfLink::class)->group(function () {
                                Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                                    Route::delete('unlink', [Api\LinkController::class, 'unlink_link'])->name('core.links.unlink');
                                });

                                Route::middleware(Middleware\ValidateNamespaceMember::class)->group(function () {
                                    Route::get('show', [Api\LinkController::class, 'show_link'])->name('core.links.show');
                                });
                            });
                        });
                    });
                });
            });


            Route::prefix('phases')->group(function () {

                Route::get('list', [Api\PhaseController::class, 'list_phases'])->name('core.phases.list');

                Route::prefix('{phase}')->group(function () {
                    Route::get('show', [Api\PhaseController::class, 'show_phase'])->name('core.phases.show');
                });

            });


            Route::prefix('waits')->group(function () {

                Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                    Route::post('create_master', [Api\WaitController::class, 'create_master'])->name('core.waits.create_master');
                });

                Route::middleware([])->group(function () {
                    Route::get('list', [Api\WaitController::class, 'list_masters'])->name('core.waits.list_masters');
                    Route::get('list_semaphores', [Api\WaitController::class, 'list_semaphores'])->name('core.waits.list_semaphores');
                    Route::get('list_waits', [Api\WaitController::class, 'list_waits'])->name('core.waits.list_waits');
                    Route::get('list_mutexes', [Api\WaitController::class, 'list_mutexes'])->name('core.waits.list_mutexes');
                });

                Route::prefix('{element_type}')->group(function () {

                    Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group(function () {
                        Route::post('update_master', [Api\WaitController::class, 'update_master'])->name('core.waits.update_master');
                        Route::post('reset_semaphore', [Api\WaitController::class, 'reset_semaphore'])->name('core.waits.reset_semaphore');
                        Route::post('ready_semaphore', [Api\WaitController::class, 'ready_semaphore'])->name('core.waits.ready_semaphore');
                    });

                    Route::middleware([])->group(function () {
                        Route::post('run', [Api\WaitController::class, 'run_master'])->name('core.waits.run_master');
                        Route::get('show', [Api\WaitController::class, 'show_master'])->name('core.waits.show_master');
                        Route::get('status', [Api\WaitController::class, 'show_master_status'])->name('core.waits.show_master_run');
                        Route::get('show_semaphore', [Api\WaitController::class, 'show_semaphore'])->name('core.waits.show_semaphore');
                        Route::get('show_wait', [Api\WaitController::class, 'show_wait'])->name('core.waits.show_wait');
                        Route::get('show_mutex', [Api\WaitController::class, 'show_mutex'])->name('core.waits.show_mutex');
                        Route::post('wait_if_available', [Api\WaitController::class, 'wait_if_available'])->name('core.waits.wait_if_available');
                        Route::post('wait_for_any', [Api\WaitController::class, 'wait_for_any'])->name('core.waits.wait_for_any');
                        Route::post('wait_for_all', [Api\WaitController::class, 'wait_for_all'])->name('core.waits.wait_for_all');
                        Route::post('wait_for_mutex', [Api\WaitController::class, 'wait_for_mutex'])->name('core.waits.wait_for_mutex');
                        Route::post('wait_for_semaphore', [Api\WaitController::class, 'wait_for_semaphore'])->name('core.waits.wait_for_semaphore');
                    });

                });
            });

            Route::prefix('elements')->group(function () {

                Route::prefix('{element}')->group(function () {
                    Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                        Route::patch('change_owner', [Api\ElementController::class, 'change_owner'])->name('core.elements.change_owner');
                        Route::delete('destroy', [Api\ElementController::class, 'destroy_element'])->name('core.elements.destroy');
                    });

                    Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group(function () {
                        Route::delete('purge', [Api\ElementController::class, 'purge_element'])->name('core.elements.purge');
                    });
                });


                Route::prefix('phase/{working_phase}')->group(function () {

                        Route::middleware([])->group(function () {
                            Route::get('list', [Api\ElementController::class, 'list_elements'])->name('core.elements.list_elements');
                        });


                        Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group(function () {
                            Route::patch('type_off', [Api\ElementController::class, 'type_off'])->name('core.elements.type_off');
                            Route::patch('type_on', [Api\ElementController::class, 'type_on'])->name('core.elements.type_on');
                        });

                        Route::prefix('element/{element}')->group(function () {

                            Route::middleware(Middleware\ValidatePhaseOfElement::class)->group(function () {
                                Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group(function () {
                                    Route::post('promote_set', [Api\ElementController::class, 'promote_set'])->name('core.elements.promote_set');

                                    Route::prefix('live')->group(function () {
                                        Route::post('promote', [Api\ElementController::class, 'promote_live'])->name('core.elements.promote_live');

                                        Route::prefix('{live_type}')->group(function () {
                                            Route::delete('demote', [Api\ElementController::class, 'demote_live'])->name('core.elements.demote_live');
                                        });
                                    });

                                });

                                Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group(function () {

                                    Route::prefix('live')->group(function () {
                                        Route::post('add', [Api\ElementController::class, 'add_live'])->name('core.elements.add_live');

                                        Route::prefix('{live_type}')->group(function () {
                                            Route::post('copy', [Api\ElementController::class, 'copy_live'])->name('core.elements.copy_live');
                                            Route::delete('remove', [Api\ElementController::class, 'remove_live'])->name('core.elements.remove_live');
                                        });
                                    });


                                    Route::middleware(Middleware\ValidatePhaseOfSet::class)->group(function () {
                                        Route::post('link/{element_set}', [Api\ElementController::class, 'create_link'])->name('core.elements.link');
                                    });

                                    Route::post('create_set', [Api\ElementController::class, 'create_set'])->name('core.elements.create_set');

                                });

                                Route::middleware([])->group(function () {
                                    Route::post('ping', [Api\ElementController::class, 'ping_element'])->name('core.elements.ping');
                                    Route::get('show', [Api\ElementController::class, 'show_element'])->name('core.elements.show');

                                });

                                Route::get('read_attribute', [Api\ElementController::class, 'read_attribute'])->name('core.elements.read_attribute');
                                Route::get('read_live_type', [Api\ElementController::class, 'read_live_type'])->name('core.elements.read_live_type');
                                Route::get('read_type', [Api\ElementController::class, 'read_type'])->name('core.elements.read_type');
                                Route::get('read_time', [Api\ElementController::class, 'read_time'])->name('core.elements.read_time');
                                Route::patch('write_attribute', [Api\ElementController::class, 'write_attribute'])->name('core.elements.write_attribute');
                            });
                        });

                });

            }); //end elements



            Route::prefix('sets')->group(function () {

                Route::prefix('phase/{working_phase}')->group(function () {
                    Route::get('list', [Api\SetController::class, 'list_sets'])->name('core.sets.list');
                    Route::prefix('set')->group(function () {
                        Route::prefix('{element_set}')->group(function () {
                            Route::middleware(Middleware\ValidatePhaseOfSet::class)->group(function () {
                                Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group(function () {
                                    Route::delete('destroy', [Api\SetController::class, 'destroy_set'])->name('core.sets.destroy_set');
                                    Route::delete('purge_set', [Api\SetController::class, 'purge_set'])->name('core.sets.purge_set');
                                    Route::delete('purge_members', [Api\SetController::class, 'purge_members'])->name('core.sets.purge_members');
                                    Route::delete('empty', [Api\SetController::class, 'empty_set'])->name('core.sets.empty_set');
                                    Route::patch('stick_element', [Api\SetController::class, 'stick_element'])->name('core.sets.stick_element');
                                    Route::patch('unstick_element', [Api\SetController::class, 'unstick_element'])->name('core.sets.unstick_element');
                                    Route::post('add_element', [Api\SetController::class, 'add_element'])->name('core.sets.add_element');
                                    Route::delete('remove_element', [Api\SetController::class, 'remove_element'])->name('core.sets.remove_element');
                                });


                                Route::middleware([])->group(function () {


                                    Route::get('show_set', [Api\SetController::class, 'show_set'])->name('core.sets.show_set');
                                    Route::get('list_children', [Api\SetController::class, 'list_children'])->name('core.sets.list_children');
                                    Route::get('list_elements', [Api\SetController::class, 'list_elements'])->name('core.sets.list_elements');

                                });
                            });
                        });
                    });


                });



            }); //end sets



            Route::prefix('types')->group(function () {


                Route::middleware([])->group(function () {
                    Route::get('list_published', [Api\TypeController::class, 'list_published'])->name('core.types.list_published');
                    Route::get('list_suspended', [Api\TypeController::class, 'list_suspended'])->name('core.types.list_suspended');
                });


                Route::prefix('{element_type}')->group(function () {

                    Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group(function () {
                        Route::patch('promote_owner', [Api\TypeController::class, 'promote_owner'])->name('core.types.promote_owner');
                        Route::patch('purge', [Api\TypeController::class, 'purge_type'])->name('core.types.purge');
                        Route::patch('promote_publish', [Api\TypeController::class, 'publish_type_promote'])->name('core.types.promote_publish');
                        Route::patch('suspend', [Api\TypeController::class, 'suspend_type'])->name('core.types.suspend');
                        Route::patch('promote_element', [Api\TypeController::class, 'promote_element'])->name('core.types.promote_element');
                    });

                    Route::middleware(Middleware\ValidateNamespaceOwner::class)->group(function () {
                        Route::patch('change_owner', [Api\TypeController::class, 'change_owner'])->name('core.types.change_owner');
                        Route::delete('destroy_type', [Api\TypeController::class, 'destroy_type'])->name('core.types.destroy_type');

                    });


                    Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group(function () {
                        Route::patch('add_handle', [Api\TypeController::class, 'add_handle'])->name('core.types.add_handle');
                        Route::patch('remove_handle', [Api\TypeController::class, 'remove_handle'])->name('core.types.remove_handle');
                        Route::post('fire_event', [Api\TypeController::class, 'fire_event'])->name('core.types.fire_event');
                        Route::patch('publish_type', [Api\TypeController::class, 'publish_type'])->name('core.types.publish_type');
                        Route::patch('retire', [Api\TypeController::class, 'retire_type'])->name('core.types.retire');
                        Route::post('create_element', [Api\TypeController::class, 'create_element'])->name('core.types.create_element');

                    });

                    Route::middleware([])->group(function () {
                        Route::get('show', [Api\TypeController::class, 'show_type'])->name('core.types.show');
                        Route::get('list_live', [Api\TypeController::class, 'list_live'])->name('core.types.list_live');
                        Route::get('list_elements', [Api\TypeController::class, 'list_elements'])->name('core.types.list_elements');
                        Route::get('list_descendants', [Api\TypeController::class, 'list_descendants'])->name('core.types.list_descendants');
                        Route::get('list_attribute_descendants', [Api\TypeController::class, 'list_attribute_descendants'])->name('core.types.list_attribute_descendants');
                    });
                });




            }); //end types


            Route::prefix('design')->group(function () {

                Route::get('list_attributes', [Api\DesignController::class, 'list_attributes'])->name('core.design.list_attributes');
                Route::get('list', [Api\DesignController::class, 'list_designs'])->name('core.design.list');

                Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group( function () {
                    Route::post('create', [Api\DesignController::class, 'create_design'])->name('core.design.create');

                    Route::prefix('schedules')->group(function () {
                        Route::get('list', [Api\DesignController::class, 'list_times'])->name('core.design.schedules.list');
                        Route::post('create', [Api\DesignController::class, 'create_time'])->name('core.design.schedules.create');
                        Route::prefix('{time_bound}')->group(function () {
                            Route::get('show', [Api\DesignController::class, 'show_schedule'])->name('core.design.schedules.show');
                            Route::patch('edit', [Api\DesignController::class, 'edit_schedule'])->name('core.design.schedules.edit');
                            Route::delete('destroy', [Api\DesignController::class, 'destroy_schedule'])->name('core.design.schedules.destroy');
                        });
                    });

                    Route::prefix('locations')->group(function () {
                        Route::get('list', [Api\DesignController::class, 'list_locatations'])->name('core.design.locations.list');
                        Route::post('create', [Api\DesignController::class, 'location_create'])->name('core.design.locations.create');
                        Route::prefix('{location_bound}')->group(function () {
                            Route::delete('destroy', [Api\DesignController::class, 'destroy_location'])->name('core.design.locations.destroy');
                            Route::patch('edit', [Api\DesignController::class, 'location_edit'])->name('core.design.locations.edit');
                        });
                    });

                });






                Route::prefix('{element_type}')->group(function () {

                    Route::middleware(Middleware\ValidateNamespaceIsSystem::class)->group( function () {
                        Route::post('promote_owner', [Api\DesignController::class, 'promote_design_owner'])->name('core.design.promote_owner');
                        Route::delete('purge', [Api\DesignController::class, 'purge_design'])->name('core.design.purge');

                        Route::prefix('attribute/{attribute}')->group(function () {
                            Route::post('promote', [Api\DesignController::class, 'create_attribute'])->name('core.design.promote_attribute');
                        });
                    });

                    Route::middleware(Middleware\ValidateNamespaceOwner::class)->group( function () {
                        Route::post('change_owner', [Api\DesignController::class, 'change_design_owner'])->name('core.design.change_owner');
                        Route::delete('destroy', [Api\DesignController::class, 'destroy_design'])->name('core.design.destroy');
                    });


                    Route::middleware(Middleware\ValidateNamespaceAdmin::class)->group( function () {
                        Route::patch('edit', [Api\DesignController::class, 'edit_design'])->name('core.design.edit');
                        Route::post('create_attribute', [Api\DesignController::class, 'create_attribute'])->name('core.design.create_attribute');
                        Route::delete('remove_parent', [Api\DesignController::class, 'remove_parent'])->name('core.design.remove_parent');
                        Route::post('add_parent', [Api\DesignController::class, 'add_parent'])->name('core.design.add_parent');

                        Route::prefix('live_rules')->group(function () {
                            Route::post('add', [Api\DesignController::class, 'add_live_rule'])->name('core.design.add_live_rule');
                            Route::delete('/{live_rule}/remove', [Api\DesignController::class, 'remove_live_rule'])->name('core.design.remove_live_rule');
                        });



                        Route::prefix('attribute/{attribute}')->group(function () {
                            Route::middleware(Middleware\ValidateAttributeOwnership::class)->group(function () {
                                Route::delete('destroy', [Api\DesignController::class, 'destroy_attribute'])->name('core.design.destroy_attribute');
                                Route::patch('edit', [Api\DesignController::class, 'edit_attribute'])->name('core.design.edit_attribute');
                                Route::post('create_listener', [Api\DesignController::class, 'create_listener'])->name('core.design.create_listener');
                                Route::delete('destroy_listener', [Api\DesignController::class, 'destroy_listener'])->name('core.design.destroy_listener');
                                Route::post('create_rule', [Api\DesignController::class, 'create_rule'])->name('core.design.create_rule');

                                Route::prefix('rule/{attribute_rule}')->group(function () {
                                    Route::middleware(Middleware\ValidateRuleOwnership::class)->group(function () {
                                        Route::post('destroy_rule', [Api\DesignController::class, 'destroy_rule'])->name('core.design.destroy_rule');
                                        Route::post('edit_rule', [Api\DesignController::class, 'edit_rule'])->name('core.design.edit_rule');
                                        Route::post('test_rule', [Api\DesignController::class, 'test_rule'])->name('core.design.test_rule');
                                    });
                                });
                            });

                        });

                    }); //end design admin


                    Route::middleware([])->group( function () {
                        Route::get('show', [Api\DesignController::class, 'show_design'])->name('core.design.show');

                        Route::get('list_listeners', [Api\DesignController::class, 'list_listeners'])->name('core.design.list_listeners');
                        Route::get('list_parents', [Api\DesignController::class, 'list_parents'])->name('core.design.list_parents');

                        Route::prefix('live_rules')->group(function () {
                            Route::get('list', [Api\DesignController::class, 'list_live_rules'])->name('core.design.list_live_rules');
                        });




                        Route::prefix('attribute/{attribute}')->group(function () {
                            Route::middleware(Middleware\ValidateAttributeOwnership::class)->group(function () {
                                Route::get('show', [Api\DesignController::class, 'show_attribute'])->name('core.design.show_attribute');

                                Route::get('show_listener', [Api\DesignController::class, 'show_listener'])->name('core.design.show_listener');
                                Route::get('test_listener', [Api\DesignController::class, 'test_listener'])->name('core.design.test_listener');
                            });
                        });


                    }); //end design members
                });




            }); //end design



        }); //end user namespace defined behavior


    }); //end auth protected



}); //end v1


require app()->basePath('libs/hbc-things/routes/thing_api.php');
require app()->basePath('libs/thangs/routes/thang_api.php');


