<?php

namespace App\Models;
use App\Models\Traits\TResourceCommon;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property int remote_time_bounds_id
 * @property int remote_location_bounds_id
 * @property ArrayObject remote_accepted_language_iso_codes
 * @property ArrayObject remote_accepted_region_iso_codes
 * @property string remote_terms_of_use_link
 * @property string remote_privacy_link
 * @property string remote_about_link
 * @property string remote_description

 *
 * @property string created_at
 * @property string updated_at
 *
 * @property User remote_owner
 * @property RemoteUri event_uri
 * @property RemoteUri default_uri
 * @property RemoteActivity[] activity_of_remote
 * @property RemoteUri[] uris
 *
 *
 */
class RemoteMetum extends Model
{
    use TResourceCommon;

    protected $table = 'remote_meta';
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
        'remote_accepted_language_iso_codes' => AsArrayObject::class,
        'remote_accepted_region_iso_codes' => AsArrayObject::class
    ];

}
