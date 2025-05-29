<?php

namespace App\Models;


use App\Enums\Attributes\TypeOfLiveAttributeBehavior;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_live_id
 * @property int earlier_attribute_id
 * @property int later_attribute_id
 * @property TypeOfLiveAttributeBehavior live_attribute_behavior
 *
 */
class LiveAttributes extends Model
{

    protected $table = 'live_attributes';
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
        'live_attribute_behavior' => TypeOfLiveAttributeBehavior::class,
    ];

}
