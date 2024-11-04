<?php

namespace App\Models;



use App\Api\Thinger\IApiThingResult;
use App\Enums\Things\TypeThingCallbackStatus;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int thing_result_id
 * @property int caller_namespace_id
 * @property int http_code_callback
 * @property TypeThingCallbackStatus thing_callback_status
 * @property string result_callback_url
 *
 *  @property string created_at
 *  @property string updated_at
 */
class ThingResultCallback extends Model
{

    protected $table = 'thing_result_callbacks';
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
        'thing_callback_status' => TypeThingCallbackStatus::class,
    ];

}
