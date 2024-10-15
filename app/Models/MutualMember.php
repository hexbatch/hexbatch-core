<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int mutual_parent_id
 * @property int from_mutual_element_id
 * @property int to_mutual_element_id
 * @property int from_source_set_id
 * @property int to_source_set_id
 *
 * @property string created_at
 * @property string updated_at
 */
class MutualMember extends Model
{

    protected $table = 'mutual_members';
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
