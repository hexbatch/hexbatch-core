<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int path_owner_id
 * @property int parent_path_id
 * @property int path_type_id
 * @property int path_attribute_id
 * @property int path_element_set_id
 * @property int path_user_type_id
 * @property int path_location_bound_id
 * @property int path_description_element_id
 * @property int path_min_gap
 * @property int path_max_gap
 * @property int path_min_count
 * @property int path_max_count
 * @property bool is_partial_matching_name
 * @property bool is_sorting_order_asc
 * @property string ref_uuid
 * @property string path_start_at
 * @property string path_end_at
 * @property string path_logic
 * @property string path_relationship
 * @property string time_comparison
 * @property string path_attribute_json_path
 * @property string path_part_name
 * @property string value_json_path
 * @property string ordering_json_path
 *
 * @property string created_at
 * @property string updated_at
 */
class Path extends Model
{

    protected $table = 'paths';
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
