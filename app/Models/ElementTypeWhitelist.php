<?php

namespace App\Models;


use App\Enums\Types\TypeOfWhitelistPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int whitelist_owning_type_id
 * @property int whitelist_namespace_id
 * @property int max_allowed
 * @property string ref_uuid
 * @property TypeOfWhitelistPermission whitelist_permission
 *
 * @property string created_at
 * @property string updated_at
 */
class ElementTypeWhitelist extends Model
{

    protected $table = 'element_type_whitelists';
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
        'whitelist_permission' => TypeOfWhitelistPermission::class,
    ];

}
