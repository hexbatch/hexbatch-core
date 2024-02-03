<?php

namespace App\Models;

use App\Models\Enums\AttributeMetaType;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int meta_parent_attribute_id
 * @property AttributeMetaType meta_type
 * @property string meta_iso_lang
 * @property string meta_mime_type
 * @property ArrayObject meta_json
 * @property string meta_value
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property Attribute meta_parent
 */
class AttributeMetum extends Model
{

    protected $table = 'attribute_meta';
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
        'meta_json' => AsArrayObject::class,
        'meta_type' => AttributeMetaType::class,
    ];

    const ANY_LANGUAGE = 'zxx';

    public function meta_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','meta_parent_attribute_id');
    }
}
