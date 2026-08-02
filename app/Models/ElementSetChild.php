<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


//todo child sets have location bounds inside the parent set, this is determined by the defining element of the set. and live can affect those
   // that means children sets have a shape or map inside the parent set that can overlap just like elements there
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_set_id
 * @property int child_set_id
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 */
class ElementSetChild extends Model
{

    protected $table = 'element_set_children';
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
