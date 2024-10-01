<?php

namespace App\Models;


use App\Enums\Types\TypeOfServerWhitelist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int server_whitelist_type_id
 * @property int to_server_id
 *
 * @property TypeOfServerWhitelist server_whitelist
 *
 * @property string created_at
 * @property string updated_at
 */
class ServerWhitelist extends Model
{

    protected $table = 'element_type_server_whitelist';
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
        'server_whitelist' => TypeOfServerWhitelist::class,
    ];

}
