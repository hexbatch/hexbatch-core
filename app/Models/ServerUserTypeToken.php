<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int token_user_type_id
 * @property int to_server_id
 * @property int last_used_at
 * @property string expires_at
 * @property string user_server_token
 * @property string user_server_public_key
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class ServerUserTypeToken extends Model
{

    protected $table = 'server_user_type_tokens';
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
