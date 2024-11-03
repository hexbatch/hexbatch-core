<?php

namespace App\Models;



use App\Api\Thinger\IApiThingResult;
use App\Enums\Things\TypeApiFollowup;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/**
 * this stores the user getting back the api info, as well as the outgoing and incoming remotes
 * when an api call is made, a row is created here to handle the results being printed out, or the polling, or pushing to a url
 * multiple results can be made for the same thing, in particular for debugging
 * todo @see IApiThingResult to have a reference here to read the action call result
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int result_thing_id
 * @property int result_to_namespace_id
 * @property int result_hex_error_id
 * @property int result_callback_status
 * @property TypeApiFollowup user_followup
 * @property string result_callback_url
 * @property string raw_result
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
        'user_followup' => TypeApiFollowup::class,
    ];

}
