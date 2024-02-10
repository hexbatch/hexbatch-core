<?php

namespace App\Models;


use App\Models\Enums\RemoteOutputMapType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property boolean is_secret
 * @property RemoteOutputMapType output_map_type
 * @property string header_var_name
 * @property string header_var_value
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class RemoteOutputMap extends Model
{

    protected $table = 'remote_output_maps';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
    protected $casts = [
        'output_map_type' => RemoteOutputMapType::class
    ];


}
