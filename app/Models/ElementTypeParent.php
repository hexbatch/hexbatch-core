<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int child_type_id
 * @property int parent_type_id
 * @property int parent_rank
 * @property int is_active
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class ElementTypeParent extends Model
{

    protected $table = 'element_type_parents';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_type_id',
        'parent_type_id',
        'parent_rank',
        'is_active'
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


    public function getName() :string {
        return $this->ref_uuid;
    }
}
