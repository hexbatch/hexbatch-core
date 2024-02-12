<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\ElementResource;
use App\Http\Resources\ElementTypeResource;
use App\Http\Resources\LocationBoundResource;
use App\Http\Resources\RemoteResource;
use App\Http\Resources\TimeBoundResource;
use App\Http\Resources\UserGroupResource;
use App\Http\Resources\UserResource;
use App\Models\Enums\Attributes\AttributeValueType;
use App\Models\Enums\Bounds\LocationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int value_parent_attribute_id
 * @property int user_id
 * @property int user_group_id
 * @property int attribute_id
 * @property int element_type_id
 * @property int element_id
 * @property int time_bound_id
 * @property int location_bound_id
 * @property int remote_id
 * @property int action_id
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property Attribute value_parent
 * @property User value_user
 * @property UserGroup value_group
 * @property Attribute value_attribute
 * @property Element value_element
 * @property ElementType value_element_type
 * @property TimeBound value_schedule
 * @property LocationBound value_location
 * @property Remote value_remote
 *
 *
 */
class AttributeValuePointer extends Model
{

    protected $table = 'attribute_value_pointers';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

    ];


    public function value_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','value_parent_attribute_id');
    }

    public function value_user() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function value_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','user_group_id');
    }

    public function value_attribute() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','attribute_id');
    }

    public function value_element() : BelongsTo {
        return $this->belongsTo('App\Models\Element','element_type_id');
    }

    public function value_element_type() : BelongsTo {
        return $this->belongsTo('App\Models\ElementType','element_type_id');
    }

    public function value_schedule() : BelongsTo {
        return $this->belongsTo('App\Models\TimeBound','time_bound_id');
    }

    public function value_location() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','location_bound_id');
    }
    public function value_remote() : BelongsTo {
        return $this->belongsTo('App\Models\Remote','remote_id');
    }

    public function getValue() {
        if ($this->value_user()->first()) {return $this->value_user;}
        if ($this->value_group()->first()) {return $this->value_group;}
        if ($this->value_attribute()->first()) {return $this->value_attribute;}
        if ($this->value_element()->first()) {return $this->value_element;}
        if ($this->value_element_type()->first()) {return $this->value_element_type;}
        if ($this->value_schedule()->first()) {return $this->value_schedule;}
        if ($this->value_location()->first()) {return $this->value_location;}
        if ($this->value_remote()->first()) {return $this->value_remote;}
        return null;
    }

    public function getValueDisplayForResource(int $n_display) {
        if ($n_display <= 1) {
            if ($this->value_user()->first()) {return $this->value_user->username;}
            if ($this->value_group()->first()) {return $this->value_group->getName();}
            if ($this->value_attribute()->first()) {return $this->value_attribute->getName();}
            if ($this->value_element()->first()) {return $this->value_element->getName();}
            if ($this->value_element_type()->first()) {return $this->value_element_type->getName();}
            if ($this->value_schedule()->first()) {return $this->value_schedule->getName();}
            if ($this->value_location()->first()) {return $this->value_location->getName();}
            if ($this->value_remote()->first()) {return $this->value_remote->getName();}
        } else {
            if ($this->value_user()->first()) {return new UserResource($this->value_user,null,$n_display - 1);}
            if ($this->value_group()->first()) {return new UserGroupResource($this->value_group,null,$n_display - 1);}
            if ($this->value_attribute()->first()) {return new AttributeResource($this->value_attribute,null,$n_display - 1);}
            if ($this->value_element()->first()) {return new ElementResource($this->value_element,null,$n_display - 1);}
            if ($this->value_element_type()->first()) {return new ElementTypeResource($this->value_element_type,null,$n_display - 1);}
            if ($this->value_schedule()->first()) {return new TimeBoundResource($this->value_schedule,null,$n_display - 1);}
            if ($this->value_location()->first()) {return new LocationBoundResource($this->value_location,null,$n_display - 1);}
            if ($this->value_remote()->first()) {return new RemoteResource($this->value_remote,null,$n_display - 1);}
        }
        return null;
    }

    /**
     * Does not save
     * @param Attribute $attribute
     * @param $maybe_value
     * @param AttributeValueType|null $hint
     * @return AttributeValuePointer
     */
    public static function createAttributeValue(Attribute $attribute,$maybe_value,?AttributeValueType $hint = null ) : AttributeValuePointer {

        $ret = new AttributeValuePointer();
        $ret->value_parent_attribute_id = $attribute->id;
        if (!$hint) { $hint = $attribute->value_type;}
        if (is_string($maybe_value)) {
            switch ($hint) {
                case AttributeValueType::USER : {
                    /** @var User $found_object */
                    $found_object = (new User())->resolveRouteBinding($maybe_value);
                    $ret->user = $found_object->id;
                    break;
                }
                case AttributeValueType::USER_GROUP : {
                    /** @var UserGroup $found_object */
                    $found_object = (new UserGroup())->resolveRouteBinding($maybe_value);
                    $ret->user_group_id = $found_object->id;
                    break;
                }
                case AttributeValueType::ATTRIBUTE : {
                    /** @var Attribute $found_object */
                    $found_object = (new Attribute())->resolveRouteBinding($maybe_value);
                    if ($found_object->is_retired) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_default_retired",['name'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $ret->attribute_id = $found_object->id;
                    break;
                }

                case AttributeValueType::REMOTE : {
                    /** @var Remote $found_object */
                    $found_object = (new Remote())->resolveRouteBinding($maybe_value);
                    if ($found_object->is_retired) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_default_retired",['name'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $ret->remote_id = $found_object->id;
                    break;
                }
                case AttributeValueType::ELEMENT : {
                    /** @var Element $found_object */
                    $found_object = (new Element())->resolveRouteBinding($maybe_value);
                    $ret->element_id = $found_object->id;
                    break;
                }
                case AttributeValueType::ELEMENT_TYPE : {
                    /** @var ElementType $found_object */
                    $found_object = (new ElementType())->resolveRouteBinding($maybe_value);
                    if ($found_object->is_retired) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_default_retired",['name'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $ret->element_type_id = $found_object->id;
                    break;
                }
                case AttributeValueType::SCHEDULE_BOUNDS : {
                    /** @var TimeBound $found_object */
                    $found_object = (new TimeBound())->resolveRouteBinding($maybe_value);
                    if ($found_object->is_retired) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_default_retired",['name'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $ret->time_bound_id = $found_object->id;
                    break;
                }
                case AttributeValueType::SHAPE_BOUNDS : {
                    /** @var LocationBound $found_object */
                    $found_object = (new LocationBound())->resolveRouteBinding($maybe_value);
                    if ($found_object->is_retired) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_default_retired",['name'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    if ($found_object->location_type !== LocationType::SHAPE) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_wrong_value",['type'=>$hint->value,'res'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $ret->location_bound_id = $found_object->id;
                    break;
                }
                case AttributeValueType::MAP_BOUNDS :
                {
                    /** @var LocationBound $found_object */
                    $found_object = (new LocationBound())->resolveRouteBinding($maybe_value);

                    if ($found_object->location_type !== LocationType::MAP) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_wrong_value",['type'=>$hint->value,'res'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    if ($found_object->is_retired) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_default_retired",['name'=>$found_object->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $ret->location_bound_id = $found_object->id;
                    break;
                }
                default: {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_unsupported_value",['type'=>$hint->value]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }

            }//end switch

            return $ret;
        } //end is string
        elseif(is_object($maybe_value)) {
            switch (get_class($maybe_value)) {
                case 'App\Models\User': {
                    $ret->user_id = $maybe_value->id;
                    break;
                }
                case 'App\Models\UserGroup': {
                    $ret->user_group_id = $maybe_value->id;
                    break;
                }
                case 'App\Models\Attribute': {
                    $ret->attribute_id = $maybe_value->id;
                    break;
                }
                case 'App\Models\ElementType': {
                    $ret->element_type_id = $maybe_value->id;
                    break;
                }
                case 'App\Models\Element': {
                    $ret->element_id = $maybe_value->id;
                    break;
                }

                case 'App\Models\TimeBound': {
                    $ret->time_bound_id = $maybe_value->id;
                    break;
                }
                case 'App\Models\LocationBound': {
                    $ret->location_bound_id = $maybe_value->id;
                    break;
                }
                default : {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_unsupported_value",['type'=>$hint->value]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
            }

            return $ret;
        } else {
            throw new \LogicException("neither string nor class");
        }
    }
}
