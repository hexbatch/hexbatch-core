<?php

namespace App\Models;


use App\Enums\Things\TypeThingHookMode;
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
 * if the hooked_thing_callback_url is null, then results logged

 */


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int hook_on_action_id
 * @property int hook_on_api_id
 * @property int hook_on_base_rule_type_id
 * @property int hook_on_base_set_type_id
 * @property int hook_on_member_namespace_id
 * @property int hook_on_admin_namespace_id
 * @property bool is_on
 * @property bool is_blocking
 *
 * @property string hooked_thing_callback_url
 * @property string ref_uuid
 * @property string thing_cluster_name
 * @property string thing_cluster_notes
 * @property ArrayObject extra_data
 * @property TypeThingHookMode thing_hook_mode
 *
 */
class ThingHook extends Model
{


    protected $table = 'thing_hooks';
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
        'thing_hook_mode' => TypeThingHookMode::class,
    ];

}
