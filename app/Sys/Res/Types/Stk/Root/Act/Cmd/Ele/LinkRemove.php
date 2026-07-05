<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\Element;
use App\Models\ElementLink;
use App\Models\ElementSet;
use App\Models\ElementType;
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
use Illuminate\Support\Facades\DB;


#[HexbatchTitle( title: "Remove a link")]
#[HexbatchBlurb( blurb: "Can unlink a set from an element")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Unlinking sets

Removes a set from an element linking to it.

Once linked, the set can be  unlinked, if no event handler for the element,
then only permission check is that the calling namespace is in element admin group

The element and set types will recieve a

   * [LinkDestroying](../../../Evt/Server/LinkDestroying.php)

If all report back ok, then the link is undone.

Once the link is removed, the element ns and type owners and set will get an event
   * [LinkDestroyed](../../../Evt/Server/LinkDestroyed.php)


')]
#[ApiParamMarker( param_class: SelectElementParamData::class)]
class LinkRemove extends Act\Cmd\Ele
{
    const UUID = 'c0f2f5b9-3030-4e60-9bd0-742299a6b83b';
    const ACTION_NAME = TypeOfAction::CMD_LINK_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\LinkDestroyed::class,
        Evt\Server\LinkDestroying::class
    ];


    public function __construct(
        protected ?SelectElementParamData $params,
        protected  ElementSet                  $given_set,
        protected bool                    $is_system,
        protected UserNamespace           $calling_namespace,

        /** @var Collection<ElementLink>|null        $selected_links */
        protected ?Collection             $selected_links = null,


    )
    {
        if (!$this->selected_links && $this->params) {
            $this->selected_links = ElementLink::getLinksFromParams(
                params: $this->params, set: $this->given_set,b_do_relations: true,cursor: $this->params->cursor);
        }

    }



    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'is_system'=> $this->is_system,
            'given_set'=> $this->given_set,
            'calling_namespace'=> $this->calling_namespace,
            'selected_links'=> $this->selected_links,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = null;
        if (!empty($args['params']??null)) {
            $params = SelectElementParamData::from($args['params']);
        }

        $is_system = (bool)$args['is_system'];
        $selected_links = static::getElementCollectionFromArray('selected_links',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $given_set = static::getSetFromArray('given_set',$args);
        return new static(
            params: $params,given_set: $given_set,
            is_system: $is_system,
            calling_namespace: $calling_namespace, selected_links: $selected_links);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $work->doUnLinkOfElements();
            foreach ($work->selected_links as $e) {
                $work->fireNotificationsForElement(e:$e,s:$work->given_set,children_args: $children_args);
            }
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'elements'=>$work->selected_links->toArray()]);
    }


    /**
     * @throws \Throwable
     */
    protected function doUnLinkOfElements()
    : void
    {

        DB::transaction(function() {
            foreach ($this->selected_links as $li) {
                $li->delete();
            }
        });


    }

    /**
     * @throws \Throwable
     */
    protected function fireNotificationsForElement(Element $e, ?ElementSet $s, array $children_args) {
        $callables = [
            Evt\Server\LinkDestroyed::class
        ];

        foreach ($callables as $callable_class) {
            $r = new $callable_class($e->of_element,$s);
            $r->callTreeByItself($children_args);
        }
    }

    /**
     * @param Collection<ElementLink> $given_links
     */
    protected static function checkPermissions(Collection $given_links, UserNamespace $calling_namespace)
    {
        $ns = [];
        foreach ($given_links as $li) {
            $ns[$li->linking_element->element_namespace->ref_uuid] = $li->linking_element->element_namespace->ref_uuid;
        }
        foreach ($ns as $a_ns) {
            static::checkIfGivenIsAdmin(given: $calling_namespace,target: $a_ns);
        }
    }


    /**
     * @throws \Throwable
     */
    public static function linkRemoveTree(
        ?SelectElementParamData    $params,
        ElementSet                $given_set,
        bool                      $is_system,
        UserNamespace             $calling_namespace,

        /** @var Collection<ElementLink>        $given_links */
        Collection|null                $given_links = null,
        ?IThangBuilder $builder = null
    ) : ElementType|Thang|IThangBuilder
    {

        if (!$is_system) {
            static::checkPermissions(given_links: $given_links, calling_namespace: $calling_namespace);
        }

        $me = new static(
            params: $params,
            given_set: $given_set,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            selected_links: $given_links
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

        if (!$is_system)
        {
            $given_set = null;
            if ($params->set_ref) {
                $given_set = ElementSet::getThisSet(uuid:$params->set_ref);
            }
            foreach ($me->selected_links as $el) {
                Evt\Server\LinkCreating::callEventTree(
                    given_element: $el,
                    given_set: $given_set,
                    builder: $builder);
            }
        }



        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  ElementType::getElementType(uuid: $data['ref_uuid']);
        } else {
            return $thang;
        }
    }


}

