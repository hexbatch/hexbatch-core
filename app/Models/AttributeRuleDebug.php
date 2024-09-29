<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int pending_thing_id
 * @property int debuggee_rule_id
 * @property int found_trigger_element_id
 * @property int found_data_element_id
 * @property int found_target_element_id
 * @property string read_data_value
 * @property string write_data_value
 *
 * @property string created_at
 * @property string updated_at
 */
class AttributeRuleDebug extends Model
{

    protected $table = 'attribute_rule_debugs';
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
