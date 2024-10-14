<?php

namespace App\Models;


use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/**
 * if the thing has a debugger, it will call this url with a data dump of it, and its data for each breakpoint or single-step
 *   if single step or breakpoint, then the children are collected, and the parent is collected but the parent is paused
 *   if step over, then just stops at each parent and not siblings
 * only one can be marked primary (turn others off)
 * if there is a primary, then each new thing is marked with this, and will not run automatically unless this is run to cursor or step over or off
 * if the debugging_callback_url is null, then results logged
 * there should be an api for server admin group to:
 *      create/remove/change debuggers
 *      apply debugger to node or branch (will set to the debugger chosen if already set)
 *      get thing node
 *      trim thing node (if child will return false to parent)
 *      toggle breakpoint on any thing node
 *      search things
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int debugging_mode
 * @property bool is_primary
 * @property string debugging_callback_url

 * @property ArrayObject extra_data

 *
 *
 */
class ThingDebug extends Model
{


    protected $table = 'thing_debugging';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'extra_data' => AsArrayObject::class,
    ];

}
