<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_user_id
 * @property int user_server_id
 * @property int user_type_id
 * @property int public_element_id
 * @property int private_element_id
 * @property int base_user_attribute_id
 * @property int user_home_set_id
 * @property int user_admin_group_id
 *
 * @property string created_at
 * @property string updated_at
 */
class UserType extends Model
{

    protected $table = 'user_types';
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
