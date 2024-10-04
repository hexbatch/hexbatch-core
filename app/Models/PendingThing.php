<?php

namespace App\Models;


use App\Enums\Things\TypeFilterSetUsage;
use App\Enums\Things\TypeOfThingStatus;
use App\Enums\Things\TypeUserFollowup;
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
 * @property int thing_event_type_id
 * @property int caller_namespace_id
 * @property int thing_event_attribute_id
 * @property int thing_rule_id
 * @property int thing_call_result_set_id
 * @property int thing_one_set_id
 * @property int thing_two_set_id
 * @property int thing_destination_set_id
 * @property int thing_type_id
 * @property int filter_type_id
 * @property int thing_element_id
 * @property int group_aggregate_source_attribute_id
 * @property int thing_element_values_id
 * @property int filter_set_id
 * @property int thing_paths
 * @property int thing_namespace_id
 * @property int thing_user_id
 * @property int thing_server_type_id
 * @property int thing_hex_error_id
 * @property string ref_uuid
 * @property string status_change_at
 * @property string callback_http_status
 * @property string callback_url
 * @property string group_operation_name
 * @property ArrayObject thing_value
 * @property TypeOfThingStatus thing_status
 * @property TypeUserFollowup user_followup
 * @property TypeFilterSetUsage filter_set_usage
 *
 * @property string created_at
 * @property string updated_at
 */
class PendingThing extends Model
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
        'user_followup' => TypeUserFollowup::class,
        'filter_set_usage' => TypeFilterSetUsage::class,
    ];

    /*
     * todo when rule rejects attr or needs attr, the response should list what is needed and why
     *  more exactly, the response to each event from the api is determined here, because we have no way to know if this is immediate or delayed return
     *  it may be a direct return, or the user may have a callback, or its polled later
     *  so, need a structured way to match responses, and data gathering for them, to finished events
     *  the best way is for each event to have its own class and interface, with setters for the input data, and getter for the response data
     */

}
