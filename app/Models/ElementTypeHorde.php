<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int horde_type_id
 * @property int horde_attribute_id
 *
 * @property boolean is_whitelisted_reading //todo implement new
 * @property boolean is_whitelisted_writing
 * @property boolean is_map_bound
 * @property boolean is_shape_bound
 * @property boolean is_time_bound
 * @property boolean is_per_set_value
 * @property boolean is_locked_to_type_editor_membership
 * @property boolean is_locked_to_element_owner_membership
 *
 * @property Attribute horde_attribute
 * @property ElementType horde_type
 */
class ElementTypeHorde extends Model
{

    protected $table = 'element_type_hordes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'horde_type_id',
        'horde_attribute_id',
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

    public function horde_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'horde_attribute_id');

    }


    public function horde_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'horde_type_id');
    }
}
