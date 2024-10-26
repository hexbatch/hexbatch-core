<?php

namespace App\Models;


use App\Enums\Things\TypeThingHookMode;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;



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
 * @property string thing_cluster_name
 * @property string thing_cluster_notes
 * @property string ref_uuid


 *
 *
 */
class ThingHookCluster extends Model
{


    protected $table = 'thing_hook_clusters';
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
    protected $casts = [];

}
