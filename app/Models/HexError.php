<?php

namespace App\Models;


use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int hex_error_code
 * @property int hex_error_line
 * @property float hex_code_version
 * @property string hex_error_message
 * @property ArrayObject hex_error_trace
 * @property string hex_error_file
 *
 * @property string created_at
 * @property string updated_at
 */
class HexError extends Model
{

    protected $table = 'hex_errors';
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
        'hex_error_trace' => AsArrayObject::class,
    ];

}
