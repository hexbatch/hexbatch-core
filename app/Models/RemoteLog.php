<?php

namespace App\Models;

use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property string remote_call_ended
 * @property int http_response_code
 * @property ArrayObject input_headers
 * @property ArrayObject input_data
 * @property ArrayObject output_data
 * @property string output_data_text
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class RemoteLog extends Model
{

    protected $table = 'remote_logs';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    ];

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
        'headers' => ArrayObject::class,
        'input_data' => ArrayObject::class,
        'output_data' => ArrayObject::class

    ];


}
