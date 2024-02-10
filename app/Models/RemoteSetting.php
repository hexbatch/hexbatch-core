<?php

namespace App\Models;


use App\Models\Enums\RemoteSettingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property boolean is_secret
 * @property RemoteSettingType pair_type
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
class RemoteSetting extends Model
{

    protected $table = 'remote_settings';
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
        'pair_type' => RemoteSettingType::class
    ];


}
