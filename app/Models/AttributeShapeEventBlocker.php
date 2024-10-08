<?php

namespace App\Models;


use App\Enums\Attributes\TypeOfShapeIntersection;
use App\Enums\Rules\TypeOfLogic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/*
can be set if namespace has admin privileges on both attributes

blocking set scope events for one attribute to another if intersect (one event or all) and the blocker has a higher z order on the intersection
both attributes can block each other, if different z order then only one applies, if same z order then logic applies
logic is based on third attribute intersecting either, this is A B
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int blocking_attribute_id
 * @property int blocked_attribute_id
 * @property int block_logic_attribute_id
 * @property int blocking_event_id
 * @property TypeOfLogic blocking_logic
 */
class AttributeShapeEventBlocker extends Model
{

    protected $table = 'attribute_shape_event_blockers';
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
