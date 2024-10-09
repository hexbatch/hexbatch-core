<?php

namespace App\Models;


use App\Enums\Rules\TypeOfLogic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/*
can be set if namespace has admin privileges on the blocked, and can read the attribute on the blocker, and if both have shapes

filter set-scoped events for one attribute to another if intersect

logic is based on third attribute intersecting either, this is A B, if that attribute is missing, then always true. This logic attribute must have a shape, it does not need to be in the set

Priority that goes first is a child branch of the event call, the priorities that go last are run when the first priority are finished.
This can act like a filter using data from higher to lower with the lowest changing the data last, or action where the data is not changed
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int filtering_attribute_id
 * @property int filtered_attribute_id
 * @property int filtering_logic_attribute_id
 * @property int filtered_event_id
 * @property int non_blocking_priority
 * @property TypeOfLogic blocking_logic
 */
class AttributeShapeEventFilter extends Model
{

    protected $table = 'attribute_shape_event_filters';
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
        'blocking_logic' => TypeOfLogic::class,
    ];

}
