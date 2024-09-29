<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int design_attribute_id
 * @property int design_type_id
 * @property int design_type_as_set_id
 * @property int design_type_as_element_id
 * @property string ref_uuid
 * @property string design_text_notes
 * @property string design_look
 * @property string design_geom
 *
 * @property string created_at
 * @property string updated_at
 */
class Design extends Model
{

    protected $table = 'designs';
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
    protected $casts = [];

}
