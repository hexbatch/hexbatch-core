<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\AttributeMetaType;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;


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
 *
 *
 *
 */
class AttributeMetum extends Model
{

    public bool $delete_mode = false;

    const STRING_VAL_ONLY = [AttributeMetaType::NAME,AttributeMetaType::URL,AttributeMetaType::RATING,AttributeMetaType::STANDARD_FAMILY];


    const PUBLIC_META = [AttributeMetaType::DESCRIPTION,AttributeMetaType::NAME,AttributeMetaType::AUTHOR,AttributeMetaType::COPYWRITE,
        AttributeMetaType::URL,AttributeMetaType::RATING];

    const ADMIN_META = [AttributeMetaType::INTERNAL,AttributeMetaType::STANDARD_FAMILY];

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
        'meta_iso_lang',
        'meta_mime_type',
        'meta_json',
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
        'meta_json' => AsArrayObject::class,
        'meta_type' => AttributeMetaType::class,
    ];

    const ANY_LANGUAGE = 'zxx';

    public function meta_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','meta_parent_attribute_id');
    }

    public function getName() :string {
        $lang = '';
        if ($this->meta_iso_lang !== static::ANY_LANGUAGE) {
            $lang = ' - ' . $this->meta_iso_lang;
        }
        return $this->meta_type->value . $lang;
    }

    public static function createMetum(Collection $c,?Attribute $parent = null,bool $b_admin = false) : AttributeMetum {
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

        if (empty($b_admin)) {
            if (!in_array($maybe_type,AttributeMetum::PUBLIC_META)) {
                throw new HexbatchNotPossibleException(__("msg.attribute_schema_admin_meta_type",["type"=>$maybe_type->value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
        }

        $maybe_value = $c->get('value');
        if (!$b_delete_mode && empty($maybe_value)) {
            throw new HexbatchNotPossibleException(__("msg.attribute_schema_empty_meta"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }

        $ret = new AttributeMetum();
        if ($parent) {
            $ret->meta_parent_attribute_id = $parent->id;
        }
        $ret->meta_type = $maybe_type;

        if ($c->has('language')) {
            $maybe_lang = $c->get('language');
            if (!empty($maybe_lang)) {
                if (!is_string($maybe_lang) || mb_strlen($maybe_lang) > 10) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_mime_meta"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
                $ret->meta_iso_lang = $maybe_lang;
            }
        }

        if (empty($ret->meta_iso_lang)) {
            $ret->meta_iso_lang = static::ANY_LANGUAGE;
        }

        if ($b_delete_mode) {
            $ret->delete_mode = true;
        } else {

            if ($c->has('mime')) {
                $raw_mime = $c->get('mime');
                if (!empty($raw_mime)) {
                    if (!is_string($raw_mime)) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_mime_meta"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                }
                $ret->meta_mime_type = (string)$raw_mime;
            }
            if ($ret->meta_mime_type && str_contains(strtolower($ret->meta_mime_type), 'json') && !in_array($ret->meta_type, static::STRING_VAL_ONLY)) {
                //test to see if value is json

                $ret->meta_mime_type = "application/json";
                if (!is_array($maybe_value) && !is_object($maybe_value)) {
                    $issues = Utilities::jsonHasErrors((string)$maybe_value);
                    if (!$issues) {
                        $ret->meta_json = json_decode($maybe_value, true);
                    } else {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_mime_json",['msg'=>$issues]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                } else {
                    $ret->meta_json = $maybe_value;
                }

            }
            if (empty($ret->meta_json)) {
                $ret->meta_value = $maybe_value;
            }
            if ($ret->meta_type === AttributeMetaType::URL) {
                $url = parse_url($ret->meta_value);

                if ($url['scheme'] !== 'https' && $url['scheme'] !== 'http') {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_mime_url"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }

                if (mb_strlen($ret->meta_value) > 100) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_mime_url"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }

                $replaced_meta_value = preg_replace('/\s+[;.]/', '_', $ret->meta_value);
                if (mb_strlen($replaced_meta_value) !== mb_strlen($ret->meta_value)) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_mime_url"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }

            }
        }

        return $ret;
    }

    public function deleteModeActivate() {
        if ($this->delete_mode) {
            AttributeMetum::where('meta_parent_attribute_id',$this->meta_parent_attribute_id)
                ->where('meta_type',$this->meta_type->value)
                ->where('meta_iso_lang',$this->meta_iso_lang)
                ->delete();
        }
    }

    public function getMetaValue()
    {
        if (!empty($this->meta_value)) {
            return $this->meta_value;
        }
        if (!empty($this->meta_json)) {
            return $this->meta_json;
        }

        return null;
    }
}
