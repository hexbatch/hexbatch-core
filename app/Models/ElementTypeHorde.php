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
