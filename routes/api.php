<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\PathController;
use App\Http\Controllers\API\ElsewhereController;
use App\Http\Controllers\API\DesignController;
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
        Route::post('/this', [ElsewhereController::class, 'me'])->name('core.servers.this');
    });



    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/me', [AuthenticationController::class, 'me'])->name('core.users.me');

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
            Route::post('/list', [ElsewhereController::class, 'list_servers'])->name('core.servers.list');
            Route::get('/get/{server}', [ElsewhereController::class, 'get_server'])->name('core.servers.get');

            /*
             * part of elsewhere
             */
        });

        Route::prefix('namespaces')->group(function () {

            Route::post('/create/{?server}', [NamespaceController::class, 'create_namespace'])->name('core.namespaces.create');

            Route::group(['prefix' => '{user_namespace}'], function () {
                Route::middleware(ValidateNamespaceOwner::class)->group( function () {
                    Route::post('/transfer/{user}', [NamespaceController::class, 'transfer_namespace'])->name('core.namespaces.transfer');
                    Route::delete('/destroy', [NamespaceController::class, 'destroy_namespace'])->name('core.namespaces.destroy');

                    Route::put('/admin/add/{target_namespace}', [NamespaceController::class, 'group_admin_add'])->name('core.groups.admin.add');
                    Route::patch('/admin/remove/{target_namespace}', [NamespaceController::class, 'group_admin_remove'])->name('core.groups.admin.remove');

                });

                Route::middleware(ValidateNamespaceAdmin::class)->group( function () {
                    Route::put('/member/add/{target_namespace}', [NamespaceController::class, 'group_member_add'])->name('core.groups.member.add');
                    Route::delete('/member/remove/{target_namespace}', [NamespaceController::class, 'group_member_remove'])->name('core.groups.member.remove');

                });

                Route::middleware(ValidateNamespaceMember::class)->group( function () {
                    Route::get('/my_namespaces', [NamespaceController::class, 'list_my_namespaces'])->name('core.namespaces.my_namespaces');
                    Route::get('/get/{levels?}', [NamespaceController::class, 'get_namespace'])->name('core.namespaces.get');
                    Route::get('/list_members/{levels?}', [NamespaceController::class, 'list_members'])->name('core.groups.list_members');

                });
            });

            /*
             Namespace
                AddAdmin.php
                AddMember.php
                Create.php
                Destroy.php
                RemoveAdmin.php
                RemoveMember.php
                ShowAdmins.php
                ShowMembers.php
                ListAll.php
                Show.php
                ShowPublic.php -- not logged in
            PurgeMember.php
            PurgeAdmin.php
            PromoteAdmin.php
            PromoteMember.php
            Purge.php
             */
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
                        Route::patch('/edit_part', [PathController::class, 'edit_part'])->name('core.paths.parts.edit');
                        Route::patch('/add_subtree', [PathController::class, 'add_part_subtree'])->name('core.paths.parts.add_subtree');
                        Route::delete('/remove_subtree', [PathController::class, 'delete_part_subtree'])->name('core.paths.parts.remove_subtree');
                    });
                });


                Route::middleware(ValidateNamespaceMember::class)->group(function (){
                    Route::get('/list', [PathController::class, 'list_paths'])->name('core.paths.list');
                    Route::get('/test', [PathController::class, 'path_test'])->name('core.paths.test');
                    Route::prefix('/{path_part}')->middleware(ValidatePartOwnership::class)->group(function () {
                        Route::get('/get/{levels?}', [PathController::class, 'get_part'])->name('core.paths.get');
                    });

                });

                /*
                 AddHandle.php
                Copy.php
                Create.php
                Destroy.php
                Edit.php
                RemoveHandle.php
                Search.php
                ShowPartTree.php
                Show.php
                ListAll.php
                Test.php
                Publish.php
                CreatePart.php
                DestroyPart.php
                EditPart.php
                TestPart.php

                 */

            }); //end paths

            Route::prefix('design')->group(function () {

                /* Design
                 AddLiveRule.php
                AddParent.php
                AddRequirement.php
                AttributeLocation.php
                Create.php
                CreateAttribute.php
                CreateListener.php
                CreateListenerRule.php
                Destroy.php
                DestroyAttribute.php
                DestroyListener.php
                DestroyListenerRule.php
                Edit.php
                EditAttribute.php
                EditListenerRule.php
                Location.php
                RemoveLiveRule.php
                RemoveParent.php
                RemoveRequirement.php
                TestListenerRule.php
                Time.php
                ListAll.php
                Show.php
                ShowAttribute.php
                ShowLiveRules.php
                ShowRequired.php
                ShowListenerRuleTree.php
                LocationTest.php
                TimeTest.php
                AttributeLocationTest.php
                ChangeOwner.php
                PromoteOwner.php
                Purge.php
                Promotion
                 */

                Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                    Route::post('/create', [DesignController::class, 'create_type'])->name('core.design.create');
                });//todo put in design guard for the edit stuff

                Route::prefix('/{element_type}')->middleware([ValidateTypeNotInUse::class])->group(function () {
                    Route::middleware(ValidateNamespaceOwner::class)->group(function () {
                        Route::delete('/destroy', [DesignController::class, 'destroy_type'])->name('core.design.destroy');
                    });

                    Route::middleware(ValidateNamespaceAdmin::class)->group(function () {
                        Route::patch('/edit', [DesignController::class, 'edit_type'])->name('core.design.edit');
                    });
                });


                Route::middleware(ValidateNamespaceMember::class)->group(function (){
                    Route::get('/{element_type}/get/{levels?}', [DesignController::class, 'get_type'])->name('core.design.get');
                    Route::get('/list', [DesignController::class, 'list_types'])->name('core.design.list');
                    Route::get('/ping_map', [DesignController::class, 'type_ping_map'])->name('core.design.ping_map');
                    Route::get('/ping_time', [DesignController::class, 'type_ping_time'])->name('core.design.ping_time');
                });


                Route::group(['prefix' => '{element_type}/attributes'], function () {


                    Route::middleware([ValidateNamespaceAdmin::class,ValidateTypeNotInUse::class])->group(function (){
                        Route::post('/create', [DesignController::class, 'new_attribute'])->name('core.design.attributes.create');

                        Route::prefix('{attribute}')->middleware(ValidateAttributeOwnership::class)->group(function () {
                            Route::patch('/edit', [DesignController::class, 'edit_attribute'])->name('coretypes..attributes.edit');

                            Route::delete('/destroy', [DesignController::class, 'delete_attribute'])->name('core.design.attributes.destroy');
                        });

                    });

                    Route::middleware(ValidateNamespaceMember::class)->group(function (){
                        Route::prefix('{attribute}')->middleware(ValidateAttributeOwnership::class)->group(function () {
                            Route::get('/get/{levels?}', [DesignController::class, 'attribute_get'])->name('core.design.attributes.get');
                            Route::get('/ping_shape', [DesignController::class, 'attribute_ping_shape'])->name('core.design.attributes.ping');
                        });

                        Route::get('/list/{filter?}', [DesignController::class, 'attributes_list'])->name('core.design.attributes.list');
                    });



                    Route::prefix('{attribute}/rules')->middleware([ValidateAttributeOwnership::class,ValidateTypeNotInUse::class])->group(function () {

                        Route::middleware(ValidateNamespaceAdmin::class)->group(function (){
                            Route::post('/create', [DesignController::class, 'create_rules'])->name('core.design.attributes.rules.create');
                            Route::patch('/update', [DesignController::class, 'update_rules'])->name('core.design.attributes.rules.update');
                            Route::delete('/delete', [DesignController::class, 'delete_rules'])->name('core.design.attributes.rules.delete');

                            Route::prefix('/{attribute_rule}')->middleware(ValidateRuleOwnership::class)->group(function () {
                                Route::patch('/edit_rule', [DesignController::class, 'edit_rule'])->name('core.design.attributes.rules.edit_rule');
                                Route::patch('/add_subtree', [DesignController::class, 'add_rule_subtree'])->name('core.design.attributes.rules.add_subtree');
                                Route::delete('/remove_subtree', [DesignController::class, 'delete_rule_subtree'])->name('core.design.attributes.rules.remove_subtree');
                            });
                        });

                        Route::middleware(ValidateNamespaceMember::class)->group(function (){
                            Route::get('/list', [DesignController::class, 'attribute_list_rules'])->name('core.design.attributes.rules.list');
                            Route::get('/test', [DesignController::class, 'rule_test'])->name('core.design.attributes.rules.test');
                            Route::prefix('/{attribute_rule}')->middleware(ValidateRuleOwnership::class)->group(function () {
                                Route::get('/get/{levels?}', [DesignController::class, 'attribute_get_rule'])->name('core.design.attributes.rules.get');
                            });

                        });
                    });
                }); //end attributes



            }); //end design

            /*
             * Type
                AddHandle.php
                AddHandleAttribute.php
                ChangeOwner.php
                DestroyType.php
                FireEvent.php
                Publish.php
                RemoveHandle.php
                RemoveHandleAttribute.php
                Retire.php
                Suspend.php
                ListPublished.php
                ListSuspended.php
                Show.php
                ShowPublic.php -- not logged in
                Purge.php
                PromoteOwner

             */

            /* Element
                Add.php
                ChangeOwner.php
                Copy.php
                Create.php
                Destroy.php
                Link.php
                Location.php
                Off.php
                On.php
                Ping.php
                Read.php
                Subtract.php
                Time.php
                TypeOff.php
                TypeOn.php
                UnLink.php
                Write.php
                WriteVisual.php
                ListElements.php
                ShowLink.php
                ListLinks.php
                Show.php
                ShowPublic.php  -- not logged in
                Purge.php

             */

            /*
             Operation
                    Combine.php
                    Mutual.php
                    Pop.php
                    Push.php
                    Shift.php
                    Unshift.php
             */


            /* Phase
                CutTree.php
                MoveTree.php
                ReplaceTree.php
                ListPhases.php
                Show.php
            Purge.php
             */

            /*
             Semaphore
                CreateMaster.php
                Ready.php
                Reset.php
                RunMaster.php
                ListMasters.php
                ListMutexes.php
                ListSemaphores.php
                ListWaits.php
                ShowMaster.php
                ShowMasterPending.php
                ShowMasterRun.php
                ShowMutex.php
                ShowSemaphore.php
                UpdateMaster.php
             */

            /*
             * Set
                AddElement.php
                AddHandle.php
                CreateSet.php
                DestroySet.php
                RemoveElement.php
                RemoveHandle.php
                ListChildren.php
                ListMembers.php
                Show.php
                ShowPublic.php -- not logged in
                Purge.php
                PurgeMember.php
                UnstickElement
                StickElement.php
                EmptySet.php

             */

            /*
             * elsewhere
                AskCredentials.php -- not logged in
                ChangeStatus.php
                GiveCredentials.php
                GiveElement.php
                GiveNamespace.php
                GiveSet.php
                GiveType.php
                Purge.php
                Register.php    -- not logged in
                Show.php
                ShowMe.php      -- not logged in
                ListElsewhere.php
                Purge.php

             */

            /*
               thing api calls (admin only), these never go through the thing queue, so there are no actions or api registered in the types, like the users
             *   create/remove/change/view hook
                  thing_hook_list
                  thing_hook_create
                  thing_hook_show
                  thing_hook_edit
                  thing_hook_remove

             * manage single stepping children with parent hooked to debugging
                 the breakpoints on are the things, and do not change status
                    the parent nodes set to debugging will get the notice
                    if nothing set, then the thing will just stop and wait
                thing_add_breakpoint (to the exact thing)
                thing_clear_breakpoint (clears on thing and all down-thing)
                thing_run (on breaking  thing)
                thing_single_step (on breaking thing)


             * list/search/view thing nodes and trees
                thing_list (top roots)
                thing_show (a tree)
                thing_inspect (a single thing)
                thing_pause|unpause for making sure the thing will wait for the debugging, or when not needed anymore

             * trim thing tree (if child will return false to parent when it runs, if root then its just gone)

             * apply rate rules
             * Apply|Remove|List rates to set|type|action|namespace|thing
             *
             *

             */


        }); //end user namespace defined behavior

/*


//todo routes to sets, elements, lives are prepended by the phase, which is the type name of the phase row

  .-.-.   .-.-.   .-.-.   .-.-.   .-.-.   .-.-.   .-.-.   .-.-
 / / \ \ / / \ \ / / \ \ / / \ \ / / \ \ / / \ \ / / \ \ / / \
`-'   `-`-'   `-`-'   `-`-'   `-`-'   `-`-'   `-`-'   `-`-'
todo Route optional parameters

  many api calls (not design, not user, many debugging thing) can take as an optional param a path or array of paths!
    * a path returning compatible things to the api call (a path returning elements for set operations, or ns tokens for ns operations or things for that...)
    * an array of paths connected by logic (0 if no matches, 1 if match(s) )
       * This allows rate limiting later by the other layers
       * only the last path (the one executing last on the left) will return its results to the api call parameters
       * the path on the right will search first, the next path to its left only goes if there is a match on the right neighbor
       * no fucking trees

  all thing routes (most api calls) also take an optional thing setting (or its json), to set the limits on the thing for that call

 */


    }); //end auth protected



}); //end v1


