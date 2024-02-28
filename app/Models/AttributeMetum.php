<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Attributes\AttributeMetaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int meta_parent_attribute_id
 * @property AttributeMetaType meta_type

 * @property string meta_value
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property Attribute meta_parent
 *
 *
 *
 */
class AttributeMetum extends Model
{

    public bool $delete_mode = false;


    protected $table = 'attribute_meta';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'meta_parent_attribute_id',
        'meta_type',
        'meta_value'
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
        'meta_type' => AttributeMetaType::class,
    ];

    const META_VALUE_MAX_LENGTH = 255;


    public static function createMetum(Collection $c,?Attribute $parent = null) : AttributeMetum {
        /*
         * type,lang,mime,value (delete)
         */

        $b_delete_mode = false;
        if (!empty($c->get('delete') ) ) {
            $b_delete_mode = true;
        }

        if ($b_delete_mode) {
            if (!$c->has('type') ) {
                throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_meta"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
        } else {
            if (!$c->has('type') || !$c->has('value')) {
                throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_meta"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
        }

        $raw_type = $c->get('type');
        if (!is_string($raw_type)) {
            throw new HexbatchNotPossibleException(__("msg.attribute_schema_unsupported_meta_type",['type'=>$raw_type]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }

        $maybe_type = AttributeMetaType::tryFrom($raw_type);
        if (!$maybe_type) {
            throw new HexbatchNotPossibleException(__("msg.attribute_schema_unsupported_meta_type",['type'=>$raw_type]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }


        $maybe_value = $c->get('value');
        if (!$b_delete_mode && empty($maybe_value)) {
            throw new HexbatchNotPossibleException(__("msg.attribute_schema_empty_meta"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }

        if (!is_string($maybe_value) || (mb_strlen($maybe_value) >= static::META_VALUE_MAX_LENGTH )) {
            throw new HexbatchNotPossibleException(__("msg.attribute_schema_empty_meta"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }

        $ret = new AttributeMetum();
        if ($parent) {
            $ret->meta_parent_attribute_id = $parent->id;
        }
        $ret->meta_type = $maybe_type;
        $ret->meta_value = Utilities::strip_tags_convert_entities($maybe_value);

        if ($b_delete_mode) {
            $ret->delete_mode = true;
        }

        return $ret;
    }

    public function deleteModeActivate() {
        if ($this->delete_mode) {
            AttributeMetum::where('meta_parent_attribute_id',$this->meta_parent_attribute_id)
                ->where('meta_type',$this->meta_type->value)
                ->delete();
        }
    }


}
