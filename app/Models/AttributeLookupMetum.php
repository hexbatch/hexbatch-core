<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Attributes\AttributeUserGroupType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 *
 *
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int metum_lookup_attribute_id
 * @property int metum_managed_by_attribute_id
 * @property int metum_lookup_metum_id
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property AttributeMetum attribute_metum
 */
class AttributeLookupMetum extends Model
{
    protected $table = 'attribute_metum_lookups';
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


    public function attribute_metum() : BelongsTo {
        return $this->belongsTo('App\Models\AttributeMetum','metum_lookup_metum_id');
    }


}
