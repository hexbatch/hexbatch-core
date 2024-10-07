<?php

namespace App\Models;



use App\Enums\Types\TypeOfApproval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int visible_type_id
 * @property int visibility_set_id
 * @property bool is_visible_for_map
 * @property bool is_visible_for_time
 * @property bool is_time_sensitive
 *
 *
 */
class ElementTypeSetVisibility extends Model
{

    protected $table = 'element_type_set_visibilities';
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

//todo fill the values in from the horde type or originating, and set this type from the type in the horde row used
}
