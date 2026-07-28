<?php

namespace App\Models;


use App\Data\ApiParams\Common\CursoratedMetaData;
use App\Data\ApiParams\Data\Elements\ElementValData;
use App\Data\ApiParams\Data\Elements\Responses\ElementReadingList;
use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


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
        $build = ElementValue::select('element_values.*')
            ->selectRaw(" extract(epoch from  element_values.created_at) as created_at_ts")
            ->selectRaw( "extract(epoch from  element_values.updated_at) as updated_at_ts")
            ->selectRaw("IF(attributes.read_json_path IS NOT NULL,filtered_data,element_values.element_value ) as da_value");


        $build->join('attributes','element_values.horde_attribute_id','=','attributes.id');

//        $build->leftJoinLateral(
//                Attribute::selectRaw('jsonb_path_query(element_values.element_value,attributes.read_json_path)')
//                ->whereNotNull('attributes.read_json_path'),
//            "filtered_data");

        $build->leftJoinLateral('jsonb_path_query(element_values.element_value,attributes.read_json_path) as filtered_data on attributes.read_json_path',"filtered_data");

        if ($b_relations)
        {
            /** @uses static::value_type(),static::value_element(),static::value_attribute() */
            $build->with('value_type','value_element','value_attribute');
        }


        if ($me_id) {
            $build->where('element_values.id', $me_id);
        }

        if ($horde_set_id) {
            $build->where(function (Builder $query) use($horde_set_id){
                $query->where('element_values.horde_set_id', $horde_set_id)->orWhereNull('element_values.horde_set_id');
            });
        }

        if ($horde_set_ref) {
            $build->leftJoin('element_sets val_set','element_values.parent_set_element_id','=','val_set.id');
            $build->where(function (Builder $query) use($horde_set_ref){
                $query->where('val_set.ref_uuid', $horde_set_ref)->orWhereNull('val_set.id');
            });
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


    /**
     * @throws \JsonException
     */
    public static function writeContextValue(
        Attribute $att,
        ?ElementSet $set,
        Element $el,
        ?array $value = null
    ) :void
    {
        if ($att->is_abstract) {return;} //does not have state
        $member_id = null;
        if ($set  && in_array($att->value_policy,[TypeOfElementValuePolicy::PER_SET,TypeOfElementValuePolicy::PER_CHILD])) {
            $member_id = (int)ElementSetMember::buildSetMember(set_id:$set->id,element_id: $el->id,b_relationship_element: false)->pluck('id')->first();
            if (!$member_id) {
                throw new HexbatchNotPossibleException(__("msg.set_does_not_have_element",
                    ['ref' => $set->ref_uuid,'ele'=>$el->ref_uuid]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ELEMENT_NOT_IN_SET);
            }
        }
        //check to see if the element type has that attribute
        $attr_found = ElementTypeIncludedAttribute::getAllAttributes(type_ids:[$el->element_parent_type_id],attribute_ids: [$att->id]);
        if (!count($attr_found)) {
            throw new HexbatchNotPossibleException(__("msg.element_not_have_attribute",
                ['ref' => $el->ref_uuid,'attr'=>$att->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ELEMENT_NOT_HAVE_ATTRIBUTE);
        }
        $element_id = $el->id;
        $set_id = $set->id;

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


        //check if value passes validation
        $att->checkDataValidation($value);
        $value_json = json_encode($value,JSON_THROW_ON_ERROR);
        ElementValue::upsert(
            [
                'horde_type_id' => $el->element_parent_type_id,
                'horde_attribute_id' => $att->id,
                'horde_element_id' => $element_id,
                'horde_set_id' => $set_id,
                'horde_set_member_id' => $member_id,
                'element_value' => $value_json,
            ],
            [
                'horde_type_id','horde_attribute_id','horde_element_id','horde_set_id','horde_set_member_id'
            ],
            [
                'element_value' => $value_json
            ]
        );
    }


    public static function readValues(?int $set_id, array $element_ids,array $attribute_ids = [], ?int $caller_namespace_id = null )
    : ?ElementReadingList
    {
        if (!$set_id) {$set_id = -1;}
        if (!$caller_namespace_id) {$caller_namespace_id = -2;}
        $clean_el_ids = Utilities::cleanMaybeIntArrayToUniqueAndSorted($element_ids);
        if (!count($clean_el_ids)) { return null;}
        $element_id_array = implode(',',$clean_el_ids);

        $attribute_where_clause = 'true';
        $clean_att_ids = Utilities::cleanMaybeIntArrayToUniqueAndSorted($attribute_ids);
        if (count($clean_att_ids)) {

            $att_id_array = implode(',',$clean_att_ids);
            $attribute_where_clause = " xx.exposed_attribute_id in ($att_id_array)";
        }

        $sql = "
            SELECT xx.exposed_attribute_id,
                   e.id AS exposed_element_id,
                   v.horde_element_id AS maybe_horde_element_id,
                   v.horde_set_id AS maybe_horde_set_id,

                   att.ref_uuid AS exposed_att_uuid,
                   e.ref_uuid AS element_uuid,
                   t.ref_uuid AS exposed_type_uuid,
                   u.ref_uuid as exposed_namespace_uuid,

                   u.namespace_name as exposed_namespace_name,
                   t.type_name AS exposed_type_name,
                   att.attribute_name AS exposed_att_name,

                   (CASE
                        WHEN att.read_json_path IS NOT NULL
                            THEN filtered_data
                        ELSE  v.element_value
                       END) AS da_value,

                    (CASE
                        WHEN att.access_policy = 'is_element_private'
                            THEN el_mems.is_admin
                        WHEN att.access_policy = 'is_element_protected'
                            THEN el_mems IS NOT NULL
                        ELSE  true
                       END) AS can_access

            FROM elements e
                     INNER JOIN element_types t ON t.id = e.element_parent_type_id
                     INNER JOIN user_namespaces u ON u.id = t.owner_namespace_id

                     INNER JOIN user_namespaces el_ns ON el_ns.id = e.element_namespace_id
                     LEFT JOIN user_namespace_members el_mems ON el_mems.parent_namespace_id = el_ns.id AND el_mems.member_namespace_id = $caller_namespace_id

                     INNER JOIN  element_type_exposed_attributes xx ON xx.exposed_type_id = t.id
                     INNER JOIN attributes att on xx.exposed_attribute_id = att.id
                     INNER JOIN element_values v on v.horde_attribute_id = xx.exposed_attribute_id AND (        v.horde_element_id IS NULL
                                                                                                            OR (v.horde_element_id = e.id AND v.horde_set_id IS NULL)
                                                                                                            OR (v.horde_element_id = e.id AND v.horde_set_id = $set_id))
                     LEFT JOIN LATERAL jsonb_path_query(v.element_value,att.read_json_path) AS filtered_data ON att.read_json_path IS NOT NULL
            WHERE e.id IN ($element_id_array)
              AND (v.horde_set_id = $set_id OR v.horde_set_id IS NULL)
              AND $attribute_where_clause

            ORDER BY element_uuid,exposed_att_uuid,maybe_horde_set_id,maybe_horde_element_id
        ";

        $res = DB::select($sql);

        $arr = [];
        $rem = [];
        foreach ($res as $p) {
            if (!$p->can_access) { continue;}

            $element_uuid = $p->element_uuid;
            $attribute_uuid = $p->exposed_att_uuid;
            $rem_key = "$element_uuid-$attribute_uuid";
            if (isset($rem[$rem_key])) { continue;}
            $rem[$rem_key] = true;

            if (!isset($arr[$element_uuid])) {
                $arr[$element_uuid] = [
                    'type_uuid'=>$p->exposed_type_uuid,
                    'type_name'=>$p->exposed_type_name,
                    'namespace_name'=>$p->exposed_namespace_name,
                    'namespace_uuid'=>$p->exposed_namespace_uuid,
                    'element_uuid'=>$element_uuid,
                    'data'=> []
                ];
            }
            $arr[$element_uuid]['data'][$p->exposed_att_name] = json_decode($p->da_value);
        }

        $obs = [];
        foreach ($arr as $what) {
            $obs[] = ElementValData::makingUsingCodeArray($what);
        }

        return new ElementReadingList(data: collect($obs),meta: null);

    }


}
