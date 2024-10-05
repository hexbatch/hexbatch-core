<?php

namespace App\Models;



use App\Enums\Things\TypeOfThingStatus;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_thing_id
 * @property int api_call_type_id
 * @property int thing_type_id
 * @property int thing_attribute_id
 * @property int thing_rule_id
 * @property int thing_set_id
 * @property int thing_element_id
 * @property int thing_path_id
 * @property int thing_namespace_id
 * @property string ref_uuid
 * @property ArrayObject thing_value
 * @property TypeOfThingStatus thing_status
 *
 * @property string created_at
 * @property string updated_at
 */
class Thing extends Model
{

    protected $table = 'pending_things';
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
        'thing_value' => AsArrayObject::class,
        'thing_status' => TypeOfThingStatus::class,
    ];

    /*
     * the response to each event from the api is determined here, because we have no way to know if this is immediate or delayed return
     *  it may be a direct return, or the user may have a callback, or its polled later
     *  so, need a structured way to match responses, and data gathering for them, to finished events
     *  todo  each event/api to have its own class and interface, with setters for the input data, and getter for the response data
     */

}
