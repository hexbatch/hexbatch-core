<?php

namespace App\Models;




use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;

/*
 * A thing may be impacted by more than one setting, this groups them up
 *


Can fine tune rates down to the lowest of each across the cluster

When the root has many, the lowest of each is used.

When a child thing has a new rate cluster, that overrides the parent
 */
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int setting_cluster_thing_id
 * @property int owning_setting_id
 *

 */
class ThingSettingCluster extends Model
{

    protected $table = 'thing_setting_clusters';
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
