<?php

namespace App\Models;


use App\Models\Enums\RemoteInputMapType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id

 * @property RemoteInputMapType map_type
 * @property string remote_json_path
 * @property string remote_xpath
 * @property string remote_regex
 * @property string holder_json_path
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class RemoteInputMap extends Model
{

    protected $table = 'remote_input_maps';
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
        'map_type' => RemoteInputMapType::class
    ];


}
