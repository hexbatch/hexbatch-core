<?php

namespace App\Models;


use App\Enums\Elements\TypeOfSetPointerMode;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/*
 * These are only made when there is a value written to an attribute for an element, or the attribute is sometimes off (type_set_visibility_id or toggled is_on)
 * if missing from the row, then pick up the value from the attribute itself
 *
 * if not a row here, then it is assumed to be always visible and on for the attribute
 *
 * if row set but type_set_visibility_id is missing, then the attribute is always visible (unless its off)
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int element_set_member_id
 * @property int element_horde_id
 * @property int type_set_visibility_id
 * @property int pointer_to_set_id
 * @property int parent_element_value_id
 * @property bool is_on
 *
 * @property TypeOfSetPointerMode set_pointer_mode
 * @property ArrayObject element_value
 * @property ArrayObject element_shape_appearance
 * @property string element_shape
 *
 * @property string value_changed_at
 *
 * @property string created_at
 * @property string updated_at
 */
class ElementValue extends Model
{

    protected $table = 'element_values';
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
        'element_value' => AsArrayObject::class,
        'element_shape_appearance' => AsArrayObject::class,
        'set_pointer_mode' => TypeOfSetPointerMode::class,
    ];

}
