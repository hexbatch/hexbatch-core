<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 *
 * todo add trigger to make only one phase the default in all the rows, all others must be false
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int phase_type_id
 * @property int edited_by_phase_id
 * @property bool is_default_phase
 * @property bool is_system
 * @property string ref_uuid
 * @property string phase_name
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class Phase extends Model
{

    protected $table = 'phases';
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

    public static function getDefaultPhase() : ?Phase {
        return Phase::where('is_default_phase',true)->first();
    }

}
