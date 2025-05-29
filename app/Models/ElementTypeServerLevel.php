<?php

namespace App\Models;


use App\Enums\Attributes\TypeOfServerAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int server_access_type_id
 * @property int to_server_id
 *
 * @property TypeOfServerAccess access_type
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property Server access_server
 * @property ElementType type_having_access
 */
class ElementTypeServerLevel extends Model
{

    protected $table = 'element_type_server_levels';
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
        'access_type' => TypeOfServerAccess::class,
    ];

    public function access_server() : BelongsTo {
        return $this->belongsTo(Server::class,'to_server_id');
    }

    public function type_having_access() : BelongsTo {
        return $this->belongsTo(ElementType::class,'server_access_type_id');
    }

}
