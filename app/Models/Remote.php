<?php

namespace App\Models;


use App\Models\Enums\RemoteUriDataFormatType;
use App\Models\Enums\RemoteUriMethod;
use App\Models\Enums\RemoteUriType;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int user_id
 * @property int usage_group_id
 * @property string ref_uuid
 * @property string remote_name
 * @property boolean is_retired
 * @property boolean is_on
 * @property int timeout_seconds
 * @property RemoteUriType uri_type
 * @property RemoteUriMethod uri_method_type
 * @property RemoteUriDataFormatType uri_data_input_format
 * @property RemoteUriDataFormatType uri_data_output_format
 * @property string uri_string
 * @property int uri_port
 * @property bool is_readable
 * @property bool is_caching
 * @property int cache_ttl_seconds
 * @property ArrayObject cache_keys
 * @property bool is_writable
 * @property int rate_limit_max_per_unit
 * @property int rate_limit_unit_in_seconds
 * @property string rate_limit_starts_at
 * @property int rate_limit_count
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class Remote extends Model
{

    protected $table = 'remotes';
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
        'uri_type' => RemoteUriType::class,
        'uri_method_type' => RemoteUriMethod::class,
        'uri_data_input_format' => RemoteUriDataFormatType::class,
        'uri_data_output_format' => RemoteUriDataFormatType::class,
        'cache_keys' => AsArrayObject::class
    ];

}
