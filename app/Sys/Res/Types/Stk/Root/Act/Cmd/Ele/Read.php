<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Elements\ElementValData;
use App\Data\ApiParams\Data\Elements\Params\ReadElementParamData;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Events\IEventReference;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\ElementTypeIncludedAttribute;
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;

#[ApiParamMarker( param_class: ReadElementParamData::class)]
#[HexbatchTitle( title: "Read one or more elements")]
#[HexbatchBlurb( blurb: "Reads zero or more attribute from a selection of elements")]
#[HexbatchDescription( description:'
Can only read attributes that caller has permission to see.

If no set context is given, then sees default values outside of any set
if the attribute is not public,
 then needs membership in the group for protected, or admin level for private

If no read events, value is from the set context of element values. Read events only called if there is permission to read attribute
')]
class Read extends Act\Cmd\Ele implements ICommandCallable
{
    const UUID = '6280f4c3-f2de-49c1-8b4e-5f3e7aab008c';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class,
    ];

    const EVENT_CLASSES = [
        Evt\Set\Reading::class
    ];

    /** @var array<string,string> $attribute_ref_has_type_ref */
    protected array $attribute_ref_has_type_ref = [];

    /** @var array<string,string> $attribute_ref_has_name */
    protected array $attribute_ref_has_name = [];

    /** @var array<string,string> $attribute_ref_has_type_name */
    protected array $attribute_ref_has_type_name = [];

    public function __construct(
        protected ReadElementParamData      $params,
        protected bool                      $is_system,
        protected UserNamespace             $calling_namespace,

        /** @var Collection<int>|null $selected_element_ids */
        protected Collection|null           $selected_element_ids = null,

        /** @var Collection<int>|null $allowed_reading_attribute_ids */
        protected Collection|null $allowed_reading_attribute_ids = null,

        /** @var Collection<IEventReference>|null $read_events */
        protected Collection|null $read_events = null

    )
    {
        if ($this->allowed_reading_attribute_ids?->count()) {return;} //do not process if just reinit


        $element_types = [];
        $elements =  Element::getElementsFromParams(params: $this->params->selector,b_ns_relations: true,
            b_type_relations: true,b_ns_type_relations: true,cursor: $this->params->selector->cursor);


        $element_ids = [];
        foreach ($elements as $el) {
            $element_ids[] = $el->id;
            $element_types[$el->element_parent_type->ref_uuid] = $el->element_parent_type;
        }
        $this->selected_element_ids = collect($element_ids);


        $included_types = array_values($element_types);
        if (!empty($this->params->read_types)) {
            $included_types = array_intersect($included_types,$this->params->read_types);
        }

        if (empty($included_types)) {return; }

        $type_query_ids = ElementType::getTypeIdsFromInput(references: $this->params->read_types,default_ns: $this->calling_namespace->ref_uuid,
            b_allow_type_ids: false);
        $read_only_these_type_ids = $type_query_ids['ids']??[];


        $read_only_these_att_ids = [];
        if (!empty($this->params->read_attributes)) {
            $att_query_ids = Attribute::getAttributeIdsFromInput(references: $this->params->read_attributes,
                default_ns: $this->calling_namespace->ref_uuid,
                b_allow_type_ids: false);
            $read_only_these_att_ids = $att_query_ids['ids']??[];
        }

        $found_attributes = ElementTypeIncludedAttribute::getAllAttributes(type_ids: $read_only_these_type_ids,attribute_ids: $read_only_these_att_ids,b_with_type: true );
        $attribute_ids = [];
        foreach ($found_attributes as $att) {
            $attribute_ids[] = $att->id;
        }

        $this->attribute_ref_has_type_ref = [];
        $this->attribute_ref_has_type_name = [];
        $attribute_id_has_ref = [];
        $type_id_attributes = [];
        foreach ($found_attributes as $found_att) {
            if (!array_key_exists($found_att->included_type_id,$type_id_attributes ) ) {
                $type_id_attributes[$found_att->included_type_id] = [];
            }
            $type_id_attributes[$found_att->included_type_id][] = $found_att->included_attribute_id;

            if (!array_key_exists($found_att->included_attribute->ref_uuid,$this->attribute_ref_has_type_ref ) ) {
                $this->attribute_ref_has_type_ref[$found_att->included_attribute->ref_uuid] = $found_att->included_type->ref_uuid;
            }

            if (!array_key_exists($found_att->included_attribute->ref_uuid,$this->attribute_ref_has_type_name ) ) {
                $this->attribute_ref_has_type_name[$found_att->included_attribute->ref_uuid] = $found_att->included_type->type_name;
            }

            if (!array_key_exists($found_att->included_attribute->ref_uuid,$this->attribute_ref_has_name ) ) {
                $this->attribute_ref_has_name[$found_att->included_attribute->ref_uuid] = $found_att->included_attribute->attribute_name;
            }

            if (!array_key_exists($found_att->included_attribute_id,$attribute_id_has_ref ) ) {
                $attribute_id_has_ref[$found_att->included_attribute_id] = $found_att->included_attribute->ref_uuid;
            }
        }

        $attribute_id_element_ids = [];
        $attribute_id_element_refs = [];
        /** @var array<int,Element> $attribute_id_elements */
        $attribute_id_elements = [];

        foreach ($elements as $el) {
            $attribute_ids_from_type = $type_id_attributes[$el->element_parent_type_id]??[];

            foreach ($attribute_ids_from_type as $attr_id) {
                if (!array_key_exists($attr_id,$attribute_id_element_ids)) {
                    $attribute_id_element_ids[$attr_id] = [];
                    $attribute_id_elements[$attr_id] = [];
                }
                $attribute_id_element_ids[$attr_id][] = $el->id;
                $attribute_id_elements[$attr_id] = $el;

                if (!array_key_exists($attr_id,$attribute_id_element_refs)) {
                    $attribute_id_element_refs[$attr_id] = [];
                }
                $attribute_id_element_refs[$attr_id][] = $el->ref_uuid;
            }

        }
        //we need to later remove elements that have no attributes in our reading list


        if (!$this->is_system) {
            //silently filter out what cannot read
            $allowed_ns_ids = [];
            $all_ns_ids = [];
            $protected_namespace_ids = [];
            $private_namespaces_ids = [];
            $ns_attribute_id_hash = [];

            foreach ($found_attributes as $found_att) {
                $ns_id = $found_att->included_type->owner_namespace_id;

                if (!in_array($found_att->included_type->owner_namespace_id,$all_ns_ids)) {
                    $all_ns_ids[] = $ns_id;
                }

                if (!array_key_exists($ns_id,$ns_attribute_id_hash ) ) {
                    $ns_attribute_id_hash[$ns_id] = [];
                }

                $ns_attribute_id_hash[$ns_id][] = $found_att->id;


                switch ($found_att->access_policy) {
                    case TypeOfServerAccess::IS_PUBLIC_DOMAIN:
                    case TypeOfServerAccess::IS_PUBLIC: {
                        if (!in_array($found_att->included_type->owner_namespace_id,$allowed_ns_ids)) {
                            $allowed_ns_ids[] =$found_att->included_type->owner_namespace_id;
                        }
                        break;
                    }
                    case TypeOfServerAccess::IS_PROTECTED: {
                        if (!in_array($found_att->included_type->owner_namespace_id,$protected_namespace_ids)) {
                            $protected_namespace_ids[] =$found_att->included_type->owner_namespace_id;
                        }

                        break;
                    }
                    case TypeOfServerAccess::IS_ELEMENT_PROTECTED: {
                        /** @var Element $at_el */
                        foreach ($attribute_id_elements[$found_att->id] as $at_el) {
                            if (!in_array($at_el->element_namespace_id,$protected_namespace_ids)) {
                                $protected_namespace_ids[] =$at_el->element_namespace_id;
                            }
                        }
                        break;
                    }
                    case TypeOfServerAccess::IS_ELEMENT_PRIVATE: {
                        /** @var Element $at_el */
                        foreach ($attribute_id_elements[$found_att->id] as $at_el) {
                            if (!in_array($at_el->element_namespace_id,$private_namespaces_ids)) {
                                $private_namespaces_ids[] =$at_el->element_namespace_id;
                            }
                        }
                        break;
                    }
                    case TypeOfServerAccess::IS_PRIVATE: {
                        if (!in_array($found_att->included_type->owner_namespace_id,$private_namespaces_ids)) {
                            $private_namespaces_ids[] =$found_att->included_type->owner_namespace_id;
                        }
                        break;
                    }
                }




            } //end foreach

            $protected_namespace_ids = array_unique($protected_namespace_ids,SORT_NUMERIC);
            $private_namespaces_ids = array_unique($private_namespaces_ids,SORT_NUMERIC);

            if (count($private_namespaces_ids)) {
                $allowed_private_ids = $this->calling_namespace->getMemberIdsFromArray(namespace_ids: $private_namespaces_ids,t_admin: true);
                $allowed_ns_ids = array_merge($allowed_ns_ids,$allowed_private_ids);
            }

            if (count($protected_namespace_ids)) {
                $allowed_protected_ids = $this->calling_namespace->getMemberIdsFromArray(namespace_ids: $protected_namespace_ids);
                $allowed_ns_ids = array_merge($allowed_ns_ids,$allowed_protected_ids);
            }

            $allowed_ns_ids = array_unique($allowed_ns_ids,SORT_NUMERIC);
            //get the ns we are not allowed, and remove those attributes
            $remove_these_ns_ids = array_diff($all_ns_ids,$allowed_ns_ids);

            foreach ($remove_these_ns_ids as $no_id) {
                $attribute_ids_to_remove = $ns_attribute_id_hash[$no_id]??[];
                if (count($attribute_ids_to_remove)) {
                    $attribute_ids = array_diff($attribute_ids,$attribute_ids_to_remove);
                }
                foreach ($attribute_ids_to_remove as $remove_me_id) {
                    unset($attribute_id_element_ids[$remove_me_id]);
                    unset($attribute_id_element_refs[$remove_me_id]);
                }
            }

            //regenerate element_id list
            $new_element_ids = [];
            foreach ($attribute_id_element_ids as $element_id_list) {
                $new_element_ids = array_merge($new_element_ids,$element_id_list);
            }

            $this->selected_element_ids = collect(array_unique($new_element_ids,SORT_NUMERIC));

        }



        $this->read_events = Attribute::getEventHandlerRefsFromAttributes(event_type: TypeOfEvent::ATTRIBUTE_READING,attribute_ids: $attribute_ids);

        $event_attribute_ids = [];
        foreach ($this->read_events as $ev) {
            $event_attribute_ids[] = $ev->getSourceId();
            $ev->setReferences($attribute_id_element_refs[$ev->getSourceId()]??[]);
            $ev->setSourceRef($attribute_id_has_ref[$ev->getSourceId()]);
        }
        //remove the read events from the attribute ids
        if (count($event_attribute_ids)) {
            $attribute_ids = array_diff($attribute_ids,$event_attribute_ids);
        }

        $this->allowed_reading_attribute_ids = collect($attribute_ids);


    }


    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'selected_element_ids'=>$this->selected_element_ids?->toArray(),
            'allowed_reading_attribute_ids'=>$this->allowed_reading_attribute_ids?->toArray(),
            'is_system'=>$this->is_system,
            'calling_namespace'=>$this->calling_namespace,
            'attribute_ref_has_type_name'=>$this->attribute_ref_has_type_name,
            'attribute_ref_has_type_ref'=>$this->attribute_ref_has_type_ref,
            'attribute_ref_has_name'=>$this->attribute_ref_has_name,
        ];
    }
    protected static function fromArray(array $args)
    : static
    {
        $params = ReadElementParamData::from($args['params']);
        $is_system = (bool)$args['is_system'];
        $allowed_reading_attribute_ids = static::getCollectionFromArray('allowed_reading_attribute_ids',$args,false);
        $selected_element_ids = static::getCollectionFromArray('selected_element_ids',$args,false );
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $attribute_ref_has_type_ref = $args['attribute_ref_has_type_ref']??[];
        $attribute_ref_has_type_name = $args['attribute_ref_has_type_name']??[];
        $attribute_ref_has_name = $args['attribute_ref_has_name']??[];
        $node =  new static(params: $params,is_system: $is_system,calling_namespace: $calling_namespace,selected_element_ids: $selected_element_ids,
            allowed_reading_attribute_ids: $allowed_reading_attribute_ids);
        $node->attribute_ref_has_type_ref = $attribute_ref_has_type_ref;
        $node->attribute_ref_has_type_name = $attribute_ref_has_type_name;
        $node->attribute_ref_has_name = $attribute_ref_has_name;
        return $node;
    }




    /**
     * @param array{element_ref_values:array} $children_args
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {

        /*
         * return is organized by element_ref_values key having hash element_ref => [attribute_ref,data]
         */
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        $ret = null;
        if ($b_approved) {
            $set = $work->params?->selector?->set_ref ? ElementSet::getThisSet(uuid: $work->params?->selector?->set_ref) : null;
            $ret = ElementValue::readValues(set_id: $set?->id,
                element_ids: $work->selected_element_ids->toArray(), attribute_ids: $work->allowed_reading_attribute_ids->toArray(),caller_namespace_id: $work->calling_namespace->id );

            foreach ($children_args['element_ref_values']??[] as  $node) {
                $extra = ElementValData::makingUsingCodeArray($node);
                $ret->data->add($extra);
            }
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'read'=>$ret]);
    }



    /**
     * @throws \Throwable
     */
    public static function createReadTree(
        ReadElementParamData     $params,
        bool                      $is_system,
        UserNamespace             $calling_namespace,
        ?IThangBuilder              $builder = null
    ) : Thang|IThangBuilder
    {


        $me = new static(
            params: $params,
            is_system: $is_system,
            calling_namespace: $calling_namespace
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);





        $builder->tree(
            command_class: static::class,
            command_args: $me->toArray(),
            command_tags: [static::class],
        );

        if ($me->read_events?->count())
        {
            Evt\Set\Reading::callEventTree(
                given_set_ref: $params->selector->set_ref,
                read_events: $me->read_events,
                builder: $builder);
        }



        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        return $thang;
    }

}

