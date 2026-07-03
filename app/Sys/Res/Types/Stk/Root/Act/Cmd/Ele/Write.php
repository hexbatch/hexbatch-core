<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Elements\Params\WriteElementParamData;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Sys\TypeOfEvent;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Events\IEventReference;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementTypeIncludedAttribute;
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;


#[HexbatchTitle( title: "Write to an element")]
#[HexbatchBlurb( blurb: "Writes to an attribute of an element")]
#[HexbatchDescription( description:'
 Only api or elements from the admin group can write the value.

 If no write events, value is put into the set context of element values
')]
#[ApiParamMarker( param_class: WriteElementParamData::class)]
class Write extends Act\Cmd\Ele
{
    const UUID = '51e9a358-c2b1-4876-a518-0ab65d1be224';
    const ACTION_NAME = TypeOfAction::PRAGMA_WRITE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\AttributeWrite::class
    ];


    protected ElementSet|null $given_set = null;
    protected Attribute|null $given_attribute = null;
    public function __construct(
        protected WriteElementParamData $params,
        protected UserNamespace         $calling_namespace,
        protected bool                  $is_system,
        /** @var Collection<Element>|null $selected_elements */
        protected Collection|null       $selected_elements = null,

        /** @var Collection<IEventReference>|null $write_events */
        protected Collection|null       $write_events = null

    )
    {
        if ($this->selected_elements?->count()) {return;} //do not process if just reinit


        $this->selected_elements =  Element::getElementsFromParams(params: $this->params->selector,
            b_ns_relations: true, b_type_relations: true,b_ns_type_relations: true,
            cursor: $this->params->selector->cursor);

       if ($this->selected_elements->isEmpty()) {
           return;
       }

       $this->given_attribute = Attribute::getThisAttribute(uuid: $this->params->attribute_ref,b_do_relations: true);

        $type_hash = [];
        $elements_sorted_by_type = [];
        foreach ($this->selected_elements as $el) {

            if (!array_key_exists($el->element_parent_type_id,$elements_sorted_by_type)) {
                $elements_sorted_by_type[$el->element_parent_type_id] = [];
                $type_hash[$el->element_parent_type_id] = $el->element_parent_type;
            }

            $elements_sorted_by_type[$el->element_parent_type_id][] = $el;
        }

        $type_query_ids = array_values($elements_sorted_by_type);
        $using_type_ids = ElementTypeIncludedAttribute::filterTypeIdsByAttributes(type_ids: $type_query_ids,attribute_ids: [$this->given_attribute->id]);

        $filtered_element_array = [];
        $unused_type_ids = array_diff($type_query_ids,$using_type_ids);
        foreach ($using_type_ids as $use_type_id) {
            if (isset($elements_sorted_by_type[$use_type_id])) {
                $filtered_element_array = array_merge($filtered_element_array,$elements_sorted_by_type[$use_type_id]);
            }
        }

        foreach ($unused_type_ids as $unused_type_id) {
            unset($elements_sorted_by_type[$unused_type_id]);
            unset($type_hash[$unused_type_id]);
        }

        $ns_hash = [];
        foreach ($type_hash as $a_type) {
            if (!array_key_exists($a_type->owner_namespace_id,$ns_hash)) {
                $ns_hash[$a_type->owner_namespace_id] = [];
            }
        }
        $this->selected_elements = collect(array_values($filtered_element_array));
        if ($this->selected_elements->isEmpty()) {
            return; //check
        }


        if ($this->params->selector->set_ref) {
            $this->given_set = ElementSet::getThisSet(uuid: $this->params->selector->set_ref);
        }




        if (!$this->is_system)
        {

            $allowed_ns_ids = [];
            $all_ns_ids = [];
            $protected_namespace_ids = [];
            $private_namespaces_ids = [];

            switch ($this->given_attribute->access_policy) {
                case TypeOfServerAccess::IS_PUBLIC_DOMAIN:
                case TypeOfServerAccess::IS_PUBLIC: {
                    if (!in_array($this->given_attribute->included_type->owner_namespace_id,$allowed_ns_ids)) {
                        $allowed_ns_ids[] =$this->given_attribute->included_type->owner_namespace_id;
                    }
                    break;
                }
                case TypeOfServerAccess::IS_PROTECTED: {
                    if (!in_array($this->given_attribute->included_type->owner_namespace_id,$protected_namespace_ids)) {
                        $protected_namespace_ids[] =$this->given_attribute->included_type->owner_namespace_id;
                    }

                    break;
                }
                case TypeOfServerAccess::IS_PRIVATE: {
                    if (!in_array($this->given_attribute->included_type->owner_namespace_id,$private_namespaces_ids)) {
                        $private_namespaces_ids[] =$this->given_attribute->included_type->owner_namespace_id;
                    }
                    break;
                }
                case TypeOfServerAccess::IS_ELEMENT_PRIVATE: {
                    /** @var Element $at_el */
                    foreach ($this->selected_elements as $at_el) {
                        if (!in_array($at_el->element_namespace_id,$private_namespaces_ids)) {
                            $private_namespaces_ids[] =$at_el->element_namespace_id;
                        }
                    }
                    break;
                }
                case TypeOfServerAccess::IS_ELEMENT_PROTECTED: {
                    /** @var Element $at_el */
                    foreach ($this->selected_elements as $at_el) {
                        if (!in_array($at_el->element_namespace_id,$protected_namespace_ids)) {
                            $protected_namespace_ids[] =$at_el->element_namespace_id;
                        }
                    }
                    break;
                }
            }

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


            if (count($remove_these_ns_ids)) {
                throw new HexbatchPermissionException(__("msg.cannot_write_to_element_attribute",['ref'=>$this->given_attribute->getName(short_name: false)]),
                    Response::HTTP_FORBIDDEN,
                    RefCodes::ELEMENTS_CANNOT_HAVE_ATTR_WRITTEN);
            }

        }


        $this->write_events = Attribute::getEventHandlerRefsFromAttributes(event_type: TypeOfEvent::ATTRIBUTE_WRITE,attribute_ids: [$this->given_attribute->id]);
        if ($this->write_events->count()) {
            $element_refs = [];
            foreach ($this->selected_elements as $el) {
                $element_refs[] = $el->ref_uuid;
            }
            foreach ($this->write_events as $ev) {
                $ev->setReferences($element_refs);
                $ev->setSourceRef($this->given_attribute->ref_uuid);
            }
        }

    }


    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'calling_namespace'=>$this->calling_namespace,
            'is_system'=>$this->is_system,
            'selected_elements'=>$this->selected_elements?->toArray(),
            'given_set'=>$this->given_set,
            'given_attribute'=>$this->given_attribute,
        ];
    }
    protected static function fromArray(array $args)
    : static
    {
        $params = WriteElementParamData::from($args['params']);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $is_system = (bool)$args['is_system'];
        $selected_elements = static::getElementCollectionFromArray('selected_elements',$args );
        $given_set = static::getSetFromArray('given_set',$args,false );
        $given_attribute = static::getSetFromArray('given_attribute',$args,false );

        $node =  new static(params: $params, calling_namespace: $calling_namespace, is_system: $is_system, selected_elements: $selected_elements);
        $node->given_set = $given_set;
        $node->given_attribute = $given_attribute;
        return $node;
    }




    /**
     * @param array{element_ref_values:array} $children_args
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {

        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);

        if ($b_approved) {
            foreach ($work->selected_elements as $el) {
                if (isset($children_args['element_ref_values'][$el->ref_uuid])) { continue;}
                ElementValue::writeContextValue(att: $work->given_attribute,
                    set: $work->given_set, el: $el);
            }

        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'children'=>$children_args]);
    }



    /**
     * @throws \Throwable
     */
    public static function createWriteTree(
        WriteElementParamData $params,
        bool                      $is_system,
        UserNamespace             $calling_namespace,
        ?IThangBuilder              $builder = null
    ) : Thang|IThangBuilder
    {


        $me = new static(
            params: $params,
            calling_namespace: $calling_namespace,
            is_system: $is_system
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

        if ($me->write_events?->count())
        {
            Evt\Set\AttributeWrite::callEventTree(
                given_set_ref: $params->selector->set_ref,
                write_events: $me->write_events,
                builder: $builder);
        }


        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        return $thang;
    }

}

