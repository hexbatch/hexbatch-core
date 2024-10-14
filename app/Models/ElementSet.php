<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_set_element_id
 * @property bool has_events
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class ElementSet extends Model
{

    /*
     * sets always stay on the originating server, they can be copied to others
     *
When a parent is destroyed, its children, leafs first, are destroyed in a way that the children are done first.
Elements are updated here when the set is destroyed, unless the operation prevents this

It is possible to destroy a child set without this data merge.

Parent children can do unlimited nesting, but a child can never be a parent to the parents above it.
     */
    protected $table = 'element_sets';
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

//-affinity to attribute in set means cannot be added to a set. but +affinity offsets that, so calculated in a sum, all rules for such in a type reacting at the same time
// to all using the visible attributes/rules. Inactive attributes in the set's elements don't count
}
