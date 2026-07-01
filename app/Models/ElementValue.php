<?php

namespace App\Models;


use App\Data\ApiParams\Common\CursoratedMetaData;
use App\Data\ApiParams\Data\Elements\Responses\ElementReading;
use App\Data\ApiParams\Data\Elements\Responses\ElementReadingList;
use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Helpers\Utilities;
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
 * @property int horde_type_id
 * @property int horde_attribute_id
 * @property int horde_set_id
 * @property int horde_element_id
 * @property int horde_set_member_id
 * @property int element_horde_id

 * @property int parent_element_value_id
 *

 * @property ArrayObject element_value
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property string da_value
 *

 */
class ElementValue extends Model
{

    protected $table = 'element_values';
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
        'element_value' => AsArrayObject::class,
        'da_value' => AsArrayObject::class,
    ];

    public function value_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'horde_type_id');
    }

    public function value_element() : BelongsTo {
        return $this->belongsTo(Element::class,'horde_element_id');
    }

    public function value_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'horde_attribute_id');
    }

    public static function buildElementValue(
        ?int $me_id = null,
        ?int $horde_type_id = null,
        ?int $horde_attribute_id = null,
        array $horde_attribute_ids = [],
        array $horde_attribute_refs = [],


        ?int $horde_element_id = null,
        array $horde_element_ids = [],
        array $horde_element_refs = [],
        ?int $horde_set_id = null,
        ?string $horde_set_ref = null,
        ?int $horde_set_member_id = null,

        bool $b_relations = false

    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Element::select('element_values.*')
            ->selectRaw(" extract(epoch from  element_values.created_at) as created_at_ts")
            ->selectRaw( "extract(epoch from  element_values.updated_at) as updated_at_ts");

        if ($b_relations)
        {
            /** @uses static::value_type(),static::value_element(),static::value_attribute() */
            $build->with('value_type','value_element','value_attribute');
        }

        $build->join('attributes val_att','element_values.horde_attribute_id','=','val_att.id');

        $build->selectRaw(
            "SELECT IF(value_element.read_json_path IS NOT NULL,
                jsonb_path_query(element_values.element_value,
                 value_element.read_json_path),element_values.element_value )as da_value");


        if ($me_id) {
            $build->where('element_values.id', $me_id);
        }

        if ($horde_set_id) {
            $build->where('element_values.horde_set_id', $horde_set_id);
        }

        if ($horde_set_ref) {
            $build->join('element_sets val_set','element_values.parent_set_element_id','=','val_set.id');
        }


        if ($horde_type_id) {
            $build->where('element_values.horde_type_id', $horde_type_id);
        }

        if ($horde_attribute_id) {
            $build->where('element_values.horde_attribute_id', $horde_attribute_id);
        }

        if (count($horde_attribute_ids)) {
            $build->whereIn('element_values.horde_attribute_id', $horde_attribute_ids);
        }

        if (count($horde_attribute_refs)) {
            $build->whereIn('val_att.ref_uuid', $horde_attribute_refs);
        }

        if (count($horde_element_ids)) {
            $build->whereIn('element_values.horde_element_id', $horde_element_ids);
        }

        if (count($horde_element_refs)) {
            $build->join('elements val_el','element_values.horde_element_id','=','val_el.id');
            $build->whereIn('val_el.ref_uuid', $horde_element_refs);
        }

        if ($horde_element_id) {
            $build->where('element_values.horde_element_id', $horde_element_id);
        }

        if ($horde_set_member_id) {
            $build->where('element_values.horde_set_member_id', $horde_set_member_id);
        }


        return $build;
    }

    public static function maybeAssignStaticValue(Attribute $att) :void
    {

        if (empty($att->attribute_default_value?->getArrayCopy())) {
            return;
        }

        ElementValue::insertOrIgnore(
            [
                'horde_type_id' => $att->owner_element_type_id,
                'horde_attribute_id' => $att->id,
                'horde_element_id' => null,
                'horde_set_id' => null,
                'horde_set_member_id' => null,
                'element_value' => $att->attribute_default_value->getArrayCopy(),
            ]
        );
    }

    public static function writeContextValue(
        ElementSetMember $member,
        Attribute $att,
        ElementType $type,
        ?array $value = null,
        ?Phase $phase = null
    ) :void
    {
        if ($att->is_abstract) {return;} //does not have state
        $member_id = $member->id;
        $set_id = $member->id;
        $element_id = $member->id;
        if ($att->value_policy === TypeOfElementValuePolicy::STATIC) {
            $member_id = null;
            $set_id = null;
            $element_id = null;
        } elseif ($att->value_policy === TypeOfElementValuePolicy::PER_ELEMENT) {
            $member_id = null;
            $set_id = null;
        } elseif ($att->value_policy === TypeOfElementValuePolicy::PER_SET) {
            $member_id = null;
        } // else allow any combo

        $exposed_and_visible = ElementTypeExposedAttribute::getExposedAndVisible(
            exposed_type_id: $type->id,exposed_attribute_id: $att->id, in_set_member_id:   $member->id,phase_id: $phase?->id
        );
        if (!$exposed_and_visible) {return;}

        //check if value passes validation
        $att->checkDataValidation($value);

        ElementValue::upsert(
            [
                'horde_type_id' => $type->id,
                'horde_attribute_id' => $att->id,
                'horde_element_id' => $element_id,
                'horde_set_id' => $set_id,
                'horde_set_member_id' => $member_id,
                'element_value' => $value,
            ],
            [
                'horde_type_id','horde_attribute_id','horde_element_id','horde_set_id','horde_set_member_id'
            ],
            [
                'element_value' => $value
            ]
        );
    }




    public static function readValues(null|int|string $set_identifier,array $element_identifiers, array $attribute_identifier_filters = [],
                                             ?int $page_size = null,?string $cursor = null
    )
    : ElementReadingList
    {
        $set_id = null; $set_ref = null;
        if ($set_identifier) {
            if (Utilities::is_uuid($set_identifier)) {
                $set_ref = $set_identifier;
            } else {
                $set_id = $set_identifier;
            }
        }

        $element_ids = []; $element_refs = null;
        if (count($element_identifiers)) {
            foreach ($element_identifiers as $what) {
                if (Utilities::is_uuid($what)) {
                    $element_refs[] = $what;
                } else {
                    $element_ids[] = $what;
                }
            }
        }

        $attribute_ids = []; $attribute_refs = [];
        if (count($attribute_identifier_filters)) {
            foreach ($attribute_identifier_filters as $what) {
                if (Utilities::is_uuid($what)) {
                    $attribute_refs[] = $what;
                } elseif (ctype_digit($what)) {
                    $attribute_ids[] = $what;
                }
            }
        }

        // use cursoring
        $vally = static::buildElementValue(

            horde_attribute_ids: $attribute_ids,
            horde_attribute_refs: $attribute_refs,
            horde_element_ids: $element_ids,
            horde_element_refs: $element_refs,
            horde_set_id: $set_id,
            horde_set_ref: $set_ref,
            b_relations: true
        );

        /** @var \Illuminate\Pagination\CursorPaginator<ElementValue> $page */
        $page = $vally->cursorPaginate(perPage: $page_size?:config('hbc.pagination.default_page_size'), cursor: $cursor);
        $arr = [];
        /** @var ElementValue $p */
        foreach ($page as $p) {
            $element_uuid = $p->value_element->ref_uuid;
            if (!isset($arr[$element_uuid])) {
                $arr[$element_uuid] = [
                    'type_uuid'=>$p->value_type->ref_uuid,
                    'type_name'=>$p->value_type->type_name,
                    'element_uuid'=>$element_uuid,
                    'data'=> []
                ];
            }
            $arr[$element_uuid]['data'][$p->value_attribute->attribute_name] = $p->da_value;
        }

        $meta = new CursoratedMetaData(
            per_page:$page->perPage(),
            next_cursor: $page->nextCursor()?->encode(),
            next_page_url: $page->nextPageUrl(),
            prev_cursor: $page->previousCursor()?->encode(),
            prev_page_url: $page->previousPageUrl()
        );

        $col = new Collection;
        foreach ($arr as $value) {
            $read = new ElementReading(
                element_uuid: $value['element_uuid'],type_uuid: $value['type_uuid'],type_name: $value['type_name'],data:$value['data'] );
            $col->add($read);
        }
        return new ElementReadingList(data: $col,meta: $meta);
    }

}
