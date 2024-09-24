<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id

 */
class ElementSet extends Model
{

    protected $table = 'element_sets';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

    ];

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

//todo  -affinity to attribute in set means cannot be added to a set. but +affinity offsets that, so calculated in a sum, all rules for such in a type reacting at the same time
// to all using the visible attributes/rules. Inactive attributes in the set's elements don't count
}
