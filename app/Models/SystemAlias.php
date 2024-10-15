<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/*
     * the names for all types here are unique, same for attributes, put the english here too to enforce uniqueness. When a name is used, look up the thing it refers to.
     * when the type or attribute is listed in the resources, list the alias . Use the lang resource to refresh the names for all languages at once in the standard console

 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int system_alias_type_id
 * @property int system_alias_attribute_id

 * @property string ref_uuid
 * @property string system_alias_iso_language
 * @property string system_alias_name
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class SystemAlias extends Model
{


    protected $table = 'system_aliases';
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

}
