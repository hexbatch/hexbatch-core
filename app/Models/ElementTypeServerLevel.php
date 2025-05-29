<?php

namespace App\Models;


use App\Enums\Attributes\TypeOfServerAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int server_access_type_id
 * @property int to_server_id
 *
 * @property TypeOfServerAccess access_type
 *
 * @property string created_at
 * @property string updated_at
 */
class ElementTypeServerLevel extends Model
{

    protected $table = 'element_type_server_levels';
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
        'access_type' => TypeOfServerAccess::class,
    ];

}
