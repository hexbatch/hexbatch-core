<?php

namespace App\Models;


use App\Enums\Bounds\TypeOfLocation;
use App\Enums\Types\TypeOfIntersectionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int type_intersection_set_member_id
 * @property int intersection_earlier_type_id
 * @property int intersection_later_type_id
 * @property int intersection_earlier_live_id
 * @property int intersection_later_live_id
 *
 * @property TypeOfIntersectionCategory intersection_category
 * @property TypeOfLocation intersection_location_kind
 *
 * @property string created_at
 * @property string updated_at
 */
class ElementTypeIntersection extends Model
{

    protected $table = 'element_type_intersections';
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
    protected $casts = [
        'intersection_category' => TypeOfIntersectionCategory::class,
        'intersection_location_kind' => TypeOfLocation::class,
    ];

}
