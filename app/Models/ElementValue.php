<?php

namespace App\Models;


use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Helpers\Utilities;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/*
 * This is the only place where the data is stored for the types, elements, attributes
 * Row added when the attribute is made
 * Then extra rows are only made when there is a value written to an attribute for an element,
 *    or the value is allowed to change for each set, and the attribute of the element written to in a new set
 *    or the attribute is sometimes off (type_set_visibility_id or toggled is_on)
 *
 * if row set but type_set_visibility_id is missing, then the attribute is always visible (unless its off)
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int horde_type_id
 * @property int horde_originating_type_id
 * @property int horde_attribute_id
 * @property int horde_live_attribute_id
 * @property int horde_set_id
 * @property int horde_element_id
 * @property int horde_set_member_id
 * @property int element_horde_id
 * @property int type_set_visibility_id

 * @property int parent_element_value_id
 * @property bool is_on
 *

 * @property ArrayObject element_value
 *
 * @property string value_changed_at
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property string da_json_value
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
    ];

    public static function buildElementValue(
        ?int $me_id = null,
        ?int $horde_type_id = null,
        ?int $horde_originating_type_id = null,
        ?int $horde_attribute_id = null,


        ?int $horde_element_id = null,
        ?int $horde_set_id = null,
        ?int $horde_set_member_id = null,

        ?TypeOfElementValuePolicy $policy = null,
        ?string $json_path = null,

    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Element::select('element_values.*')
            ->selectRaw(" extract(epoch from  element_values.created_at) as created_at_ts")
            ->selectRaw( "extract(epoch from  element_values.updated_at) as updated_at_ts");

        if ($json_path) {
            $build->selectRaw("SELECT jsonb_path_query(element_values.element_value, :json_path) as da_json_value",
                ['json_path'=>$json_path]);
        } else {
            $build->selectRaw( "element_values.element_value as da_json_value");
        }

        if ($me_id) {
            $build->where('element_values.id', $me_id);
        }



        if ($horde_type_id) {
            $build->where('element_values.horde_type_id', $horde_type_id);
        }

        if ($horde_originating_type_id) {
            $build->where('element_values.horde_originating_type_id', $horde_originating_type_id);
        }

        if ($horde_attribute_id) {
            $build->where('element_values.horde_attribute_id', $horde_attribute_id);
        }

        if ($policy === TypeOfElementValuePolicy::STATIC) {
            $build->whereNull('element_values.horde_element_id');
            $build->whereNull('element_values.horde_set_id');
            $build->whereNull('element_values.horde_set_member_id');
        }
        elseif ($policy === TypeOfElementValuePolicy::PER_ELEMENT) {
            if ($horde_element_id) {
                $build->where('element_values.horde_element_id', $horde_element_id);
            }
            $build->whereNull('element_values.horde_set_id');
            $build->whereNull('element_values.horde_set_member_id');
        }
        elseif ($policy === TypeOfElementValuePolicy::PER_SET) {
            if ($horde_element_id) {
                $build->where('element_values.horde_element_id', $horde_element_id);
            }
            if ($horde_set_id) {
                $build->where('element_values.horde_set_id', $horde_set_id);
            }
            $build->whereNull('element_values.horde_set_member_id');
        }
        elseif ($policy === TypeOfElementValuePolicy::PER_CHILD || !$policy) {
            if ($horde_element_id) {
                $build->where('element_values.horde_element_id', $horde_element_id);
            }

            if ($horde_set_id) {
                $build->where('element_values.horde_set_id', $horde_set_id);
            }

            if ($horde_set_member_id) {
                $build->where('element_values.horde_set_member_id', $horde_set_member_id);
            }
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
                'horde_originating_type_id' => $att->owner_element_type_id,
                'horde_attribute_id' => $att->id,
                'element_value' => $att->attribute_default_value->getArrayCopy(),
            ]
        );
    }

    public static function writeContextValue(
        ElementSetMember $member,
        Attribute $att,
        ElementType $type,
        ?ElementType $originating_type = null,
        ?array $value = null
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
        }
        //check if value passes validation
        $att->checkValidation($value);

        ElementValue::upsert(
            [
                'horde_type_id' => $type->id,
                'horde_originating_type_id' => $originating_type? $originating_type->id: $type->id,
                'horde_attribute_id' => $att->id,
                'horde_element_id' => $element_id,
                'horde_set_id' => $set_id,
                'horde_set_member_id' => $member_id,
                'element_value' => $value,
            ],
            [
                'horde_type_id','horde_originating_type_id','horde_attribute_id','horde_element_id','horde_set_id','horde_set_member_id'
            ],
            [
                'element_value' => $value
            ]
        );
    }

    public static function readContextValue(
        ElementSetMember $member,
        Attribute $att,
        ElementType $type,
        ?ElementType $originating_type = null,
    ) : string|array|null
    {


        //todo read nearest inherited attr if the base is not available, if flag set

        /** @var static[] $valrows */
        $valrows = static::buildElementValue(
            horde_type_id: $type->id,
            horde_originating_type_id: $originating_type? $originating_type->id: $type->id,
            horde_attribute_id: $att->id,
            horde_element_id: $member->member_element_id,
            horde_set_id: $member->holder_set_id,
            horde_set_member_id: $member->id,
            policy: $att->value_policy,
            json_path: $att->read_json_path

        )
            ->orderBy('id','desc')
            ->get();
        if (count($valrows) ) {
            $value_json = $valrows[0]->da_json_value;
            return Utilities::maybeDecodeJson($value_json,true);
        } else {
            if (($att->value_policy !== TypeOfElementValuePolicy::STATIC) && !empty($att->attribute_default_value?->getArrayCopy()) ) {
                //try to get default value if not static, because we would already have that value, if set, if it were static
                $vally = static::buildElementValue(
                    horde_type_id: $type->id,
                    horde_originating_type_id: $originating_type? $originating_type->id: $type->id,
                    horde_attribute_id: $att->id,
                    horde_element_id: $member->member_element_id,
                    horde_set_id: $member->holder_set_id,
                    horde_set_member_id: $member->id,
                    policy: TypeOfElementValuePolicy::STATIC,
                    json_path: $att->read_json_path

                )->first();

                $value_json = $vally?->da_json_value;
                return Utilities::maybeDecodeJson($value_json,true);
            }

            return null;
        }

    }

}
