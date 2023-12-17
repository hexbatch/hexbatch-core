<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int time_bound_id
 * @property int span_start
 * @property int span_stop
 *
 */
class TimeBoundSpan extends Model
{
    protected $table = 'time_bound_spans';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'span_start',
        'span_stop',
        'time_bound_id'
    ];

    public static function cleanUpOld() {
        static::whereRaw("extract(epoch from  NOW()) > span_stop")->delete();
    }
}
