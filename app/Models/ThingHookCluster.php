<?php

namespace App\Models;


use App\Enums\Things\TypeHookedThingStatus;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int hooked_thing_id
 * @property int owning_thing_hook_id
 *
 * @property int hook_http_status
 *
 * @property string ref_uuid
 * @property TypeHookedThingStatus hooked_thing_status
 * @property ArrayObject hook_data


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
    protected $casts = [
        'hook_data' => AsArrayObject::class,
        'hooked_thing_status' => TypeHookedThingStatus::class,
    ];

}
