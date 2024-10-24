<?php

use Illuminate\Support\Facades\Route;

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
