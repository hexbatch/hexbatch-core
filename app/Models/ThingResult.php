<?php

namespace App\Models;


use App\Enums\Things\TypeUserFollowup;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int result_thing_id
 * @property int result_to_namespace_id
 * @property int result_hex_error_id
 * @property int result_callback_status
 * @property TypeUserFollowup user_followup
 * @property string result_callback_url
 * @property ArrayObject result_response
 *
 *  @property string created_at
 *  @property string updated_at
 */
class ThingResult extends Model
{

    protected $table = 'thing_results';
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
        'result_response' => AsArrayObject::class,
        'user_followup' => TypeUserFollowup::class,
    ];

}
