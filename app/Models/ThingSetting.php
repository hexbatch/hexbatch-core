<?php

namespace App\Models;




use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;

/*
 * thing is marked as done when all children done, and there is no pagination id
 */
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int setting_thing_id

 * @property int thing_pagination_size
 * @property int thing_pagination_limit
 * @property int thing_depth_limit
 * @property int thing_rate_limit
 * @property int thing_backoff_page_policy
 * @property int thing_backoff_rate_policy
 * @property int thing_json_size_limit
 *
 *
 * @property string created_at
 * @property string updated_at
 *

 */
class ThingSetting extends Model
{

    protected $table = 'thing_settings';
    public $timestamps = false;

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     *
     * @var array<string, string>
     */
    protected $casts = [

    ];



}
