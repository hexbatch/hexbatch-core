<?php

namespace App\Models;


use App\Enums\Sets\TypeOfIntersectionState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int hosting_intersection_id
 * @property int moved_intersection_id
 * @property TypeOfIntersectionState from_intersection_state
 * @property TypeOfIntersectionState to_intersection_state

 * @property string created_at
 * @property string updated_at
 */
class SetMemberIntersectionChanges extends Model
{

    protected $table = 'set_member_intersection_changes';
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
        'from_intersection_state' => TypeOfIntersectionState::class,
        'to_intersection_state' => TypeOfIntersectionState::class,
    ];

    public function owning_hosting_intersection() : BelongsTo {
        return $this->belongsTo(ElementTypeIntersection::class,'hosting_intersection_id');
    }

    public function owning_moved_intersection() : BelongsTo {
        return $this->belongsTo(ElementTypeIntersection::class,'moved_intersection_id');
    }

}
