<?php

namespace App\Models;


use App\Enums\Elements\TypeOfSetPointerMode;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/*
 * This is the only place where the data is stored for the types, elements, attributes
 * Row added when the attribute is made
 * Then extra rows are only made when there is a value written to an attribute for an element,
 *    or the value is allowed to change for each set, and the attribute of the element written to in a new set
 *    or the attribute is sometimes off (type_set_visibility_id or toggled is_on)
 *
 * if row set but type_set_visibility_id is missing, then the attribute is always visible (unless its off)
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int horde_type_id
 * @property int horde_originating_type_id
 * @property int horde_attribute_id
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
