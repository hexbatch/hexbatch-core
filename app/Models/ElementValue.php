<?php

namespace App\Models;


use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_element_id
 * @property int element_horde_id
 * @property int containing_set_id
 * @property int pointer_to_set_id
 * @property int parent_element_value_id
 * @property bool is_on
 * @property bool is_const
 *
 * @property ArrayObject element_value
 * @property string element_shape
 *
 * @property string dynamic_value_changed_at
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
    ];

}
