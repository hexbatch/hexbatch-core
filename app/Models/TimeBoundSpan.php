<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int time_bound_id
 * @property string time_slice_range
 *
 * @property int bound_start_ts
 * @property int bound_stop_ts
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
    protected $fillable = [];

    protected $casts = [];

    public static function cleanUpOld() {
        static::whereRaw("tstzrange( null,now() - interval '1 hour') &&  time_slice_range ")->delete();
    }
}

//https://www.postgresql.org/docs/current/rangetypes.html
// https://blog.meetbrackets.com/ranges-in-laravel-7-using-postgresql-c4bc69b91758
