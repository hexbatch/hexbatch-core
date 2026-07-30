<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int live_target_element_id
 * @property int live_applied_type_id
 * @property int live_applied_in_set_id
 * @property int masking_live_id
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 */
class LiveApply extends Model
{

    protected $table = 'live_applied';
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];



    public function live_apply_element() : BelongsTo {
        return $this->belongsTo(Phase::class,'live_target_element_id');
    }

    public function live_apply_type() : BelongsTo {
        return $this->belongsTo(Phase::class,'live_applied_type_id');
    }

    public function live_apply_set() : BelongsTo {
        return $this->belongsTo(Phase::class,'live_applied_in_set_id');
    }

    public function live_apply_mask() : BelongsTo {
        return $this->belongsTo(Phase::class,'masking_live_id');
    }

}
