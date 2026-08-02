<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivePermission extends Model
{

    protected $table = 'live_permissions';
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
        'updated_at' => 'datetime',
        'can_add_listeners' => 'boolean',
        'can_add_bounds' => 'boolean',
    ];

    public function permission_giver() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'live_permission_giver_ns_id');
    }

    public function permission_trigger() : BelongsTo {
        return $this->belongsTo(ElementType::class,'live_permission_trigger_type_id');
    }

    public function permission_target() : BelongsTo {
        return $this->belongsTo(ElementType::class,'live_permission_target_type_id');
    }

}
