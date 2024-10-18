<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/*
 * Records the intersections of shapes in the attributes when they enter/leave on/off in a set
 *
 * Shapes from other attributes in the set can be added to the original shape by live types
 *  When the added attribute is turned off, or the element leaves the set, intersection is removed
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int shape_set_member_id
 * @property int shape_entry_attribute_id
 * @property int shape_exist_attribute_id
 */
class AttributeShapeIntersection extends Model
{

    protected $table = 'attribute_shape_intersections';
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * @var array<string, string>
     */
    protected $casts = [];

}
