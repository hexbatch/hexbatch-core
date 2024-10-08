<?php

namespace App\Models;


use App\Enums\Attributes\TypeOfShapeIntersection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/*
 * Records the intersections of shapes in the attributes when they enter/leave on/off in a set
 *
 * Shapes from other attributes in the set can be added to the original shape
 *  When the added attribute is turned off, or the element leaves the set, this live attribute is removed
 *  there is no event triggered when a live shape is added or removed
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int shape_set_member_id
 * @property int shape_entry_attribute_id
 * @property int shape_exist_attribute_id
 * @property int shape_z_order_for_events
 * @property TypeOfShapeIntersection kind_shape_intersection
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
    protected $casts = [
        'kind_shape_intersection' => TypeOfShapeIntersection::class,
    ];

}
