<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_thing_id
 * @property int api_call_type_id
 * @property int caller_user_type_id
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
 * @property int thing_user_type_id
 * @property int thing_server_type_id
 * @property int thing_hex_error_id
 * @property string ref_uuid
 * @property string thing_to_do
 * @property string thing_status
 * @property string user_followup
 * @property string filter_set_usage
 * @property string status_change_at
 * @property string callback_http_status
 * @property string thing_value
 * @property string callback_url
 * @property string group_operation_name
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
    protected $casts = [];

}
