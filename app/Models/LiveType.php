<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int live_phase_id
 * @property int live_target_element_id
 * @property int live_applied_type_id
 * @property int live_applied_in_set_id
 * @property int masking_live_id
 * @property string ref_uuid
 * @property string live_sum_shape_geom
 * @property string live_sum_map_geom
 * @property string live_sum_shape_bounding_box
 * @property string live_sum_map_bounding_box
 *
 * @property string created_at
 * @property string updated_at
 */
class LiveType extends Model
{

    protected $table = 'live_types';
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
