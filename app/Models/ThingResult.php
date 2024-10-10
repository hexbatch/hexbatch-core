<?php

namespace App\Models;


use App\Enums\Things\TypeApiFollowup;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/*
 * this stores the user getting back the api info, as well as the outgoing and incoming remotes
 * remote pending can mark this as a failed remote by sending some well known data (depends on data format)
 * if the data is not in json, then the result will convert it to json. The incoming or outgoing remote will have its raw text stored
 *  The raw text is converted by handler here (xml ->json) (plain text -- json) (headers -> json) (response code -> json)
 *  and that json will be sent to the thing id as the data, and then the child will hand that to its parent...
 *
 * only url domains that are not localhost can be made as remote calls, ip domains are not allowed.
 * remote attributes split up the different parts of the url,
 *   domain for example only allows periods for punctuation
 *   the port is numeric only
 *   and the schema is only http or https
 */

/**
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
