<?php

namespace App\Models;


use App\Enums\Attributes\TypeOfAttributeAccess;
use App\Enums\Attributes\AttributeRuleType;
use App\Enums\Bounds\TypeOfLocation;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int horde_type_id
 * @property int horde_attribute_id
 *
 * @property boolean is_whitelisted_reading
 * @property boolean is_whitelisted_writing
 * @property boolean is_map_bound
 * @property boolean is_shape_bound
 * @property boolean is_time_bound
 * @property boolean is_per_set_value
 * @property boolean is_access_type_editor
 * @property boolean is_access_element_owner
 *
 * @property Attribute horde_attribute
 */
class ElementTypeHorde extends Model
{

    protected $table = 'element_type_hordes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'horde_type_id',
        'horde_attribute_id',
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
    protected $casts = [];

    public function horde_attribute() : BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'horde_attribute_id');
    }

    public static function addAttribute(Attribute $attribute,ElementType $type) :ElementTypeHorde {
        $horde = new ElementTypeHorde();

        $horde->upsert([
            'horde_attribute_id' => $attribute->id,
            'horde_type_id' => $type->id,

            'is_whitelisted_reading' => (bool)$type->read_whitelist_group, //todo redo this group stuff
            'is_whitelisted_writing' => (bool)$type->write_whitelist_group,
            'is_map_bound' => ($attribute->attribute_location_bound && $attribute->attribute_location_bound->location_type === TypeOfLocation::MAP),
            'is_shape_bound' => ($attribute->attribute_location_bound && $attribute->attribute_location_bound->location_type === TypeOfLocation::SHAPE),
            'is_time_bound' => (bool)$attribute->attribute_time_bound,
            'is_per_set_value' => $attribute->is_per_set_value,
            'is_access_type_editor' => $attribute->attribute_access_type === TypeOfAttributeAccess::TYPE_PRIVATE,
            'is_access_element_owner' => $attribute->attribute_access_type === TypeOfAttributeAccess::ELEMENT_PRIVATE
        ],['horde_type_id','horde_attribute_id']);
        $horde->refresh();
        return $horde;
    }

    public static function checkAttributeConflicts(ElementType $parent) :void {

        /*
         get each attribute and run its rules for AttributeRuleType::REQUIRED
         */

        foreach ($parent->type_attributes as $att) {
            foreach ($att->rule_bundle->rules_in_group as $rule) {
                if ($rule->rule_type === AttributeRuleType::REQUIRED) {
                    $res = $rule->checkRequired($parent);
                    if (!$res) {
                        throw new HexbatchPermissionException(__("msg.attribute_rule_requirement_failed",['ref'=>$att->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                            RefCodes::ATTRIBUTE_CANNOT_EDIT);
                    }
                }
            }
        }

        //finally update the map info in the type
        $parent->sumMapFromAttributes();
    }


    public static function getDecendants(Attribute $attribute) : Builder {

        $attr_id = $attribute->id;
        $type_id = $attribute->owner_element_type_id;
        $res = DB::select(
            "
            WITH RECURSIVE rec (id) as
                   (SELECT attributes.id,
                           attributes.attribute_name,
                           attributes.parent_attribute_id
                    from attributes
                    where attributes.parent_attribute_id = $attr_id

                    UNION ALL

                    SELECT attributes.id,
                           attributes.attribute_name,
                           attributes.parent_attribute_id
                    from rec,
                         attributes
                    INNER JOIN  element_type_hordes h ON h.horde_attribute_id = attributes.id AND h.horde_type_id = $type_id
                    where attributes.id = rec.parent_attribute_id)

            SELECT id, attribute_name,parent_attribute_id
            FROM rec
            ;
            "
        );

        $ids = [];
        foreach ($res as $row) {
            $ids[] = $row->id;
        }
        /**
         * @var Builder $builder
         */
        $builder =  Attribute::whereIn('id',$ids);
        return $builder;
    }

}
