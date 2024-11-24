<?php

namespace App\Models;



use App\Api\Calls\IApiThingResult;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * this stores the user getting back the api info, as well as the outgoing and incoming remotes
 * when an api call is made, a row is created here to handle the results being printed out, or the polling, or pushing to a url
 * multiple results can be made for the same thing, in particular for debugging
 * todo @see IApiThingResult to have a reference here to read the action call result
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_thing_id

 * @property int result_hex_error_id
 * @property int result_http_status
 * @property ArrayObject result_response
 *
 *  @property string created_at
 *  @property string updated_at
 *  @property HexError hex_error
 *  @property Thing result_owner_thing
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
    ];

    public function hex_error() : BelongsTo {
        return $this->belongsTo(HexError::class,'result_hex_error_id','id');
    }

    public function result_owner_thing() : BelongsTo {
        return $this->belongsTo(Thing::class,'owner_thing_id','id');
    }



}
