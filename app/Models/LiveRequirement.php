<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
The required type must be on (and not turned off) to satisfy
 *
 *
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int live_requirement_owner_type_id
 * @property int live_requirement_needed_type_id
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class LiveRequirement extends Model
{

    protected $table = 'live_requirements';
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
