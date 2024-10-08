<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int visible_type_id
 * @property int visibility_set_id
 * @property bool is_visible_for_map
 * @property bool is_visible_for_time
 * @property bool is_time_sensitive
 *
 *
 */
class ElementTypeSetVisibility extends Model
{

    protected $table = 'element_type_set_visibilities';
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

//todo fill the values when element goes into a set and there is some bounds for one or more subtypes or the type itself
// when the e leaves the set, and there are no more types there, then remove the row
}
