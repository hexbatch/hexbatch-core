<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\Traits;

use App\Data\ApiParams\Data\Namespaces\Params\ChangeNamespacesParamData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Models\Phase;
use App\Models\User;
use App\Models\UserNamespace;
use App\Sys\Res\Atr\Stk\Placeholder\MarkerData;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\St\SetMemberAdd;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\ElementCreate;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

trait NamespaceStartAction
{
    public function __construct(
        protected ChangeNamespacesParamData $params,
        protected UserNamespace             $target_namespace,
        protected UserNamespace             $calling_namespace,
        protected ?User                     $target_user = null
    )
    {
        if (!$this->params->new_owner_user_uuid && $this->target_user) {
            $this->params->new_owner_user_uuid = $this->target_user->ref_uuid;
        }

        if (!$this->params->new_owner_user_uuid ) {
            $this->params->new_owner_user_uuid = $this->target_namespace->owner_user->ref_uuid;
        }
    }


    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'target_namespace'=> $this->target_namespace,
            'calling_namespace'=> $this->calling_namespace,
            'target_user'=> $this->target_user,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = null;
        if (!empty($args['params']??null)) {
            $params = ChangeNamespacesParamData::makingUsingCodeArray($args['params']);
        }
        $target_namespace = static::getNamespaceFromArray('target_namespace',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $target_user = static::getUserFromArray('target_user',$args, false);

        return new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace,
            target_user: $target_user
        );
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        $info = null;
        if ($b_approved) {
            $info = $work->doAction();
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: $info->toArray());
    }

    /**
     * @throws \Throwable
     * does not reload ns after attaching stuff together
     */
    public function doAction(): ChangeNamespacesParamData
    {
        $marker = ElementType::getElementType(uuid: static::MARKER_CLASS::getTypeUuid());
        $phase = Phase::getDefaultPhase();
        $attr = Attribute::getThisAttribute(uuid: MarkerData::getAttributeUuid());
        $home_type_element_factory = new ElementCreate(element_type: $marker, phase: $phase, number_to_create: 1,
            owner_namespace: $this->target_namespace, is_system: true, calling_namespace: $this->calling_namespace, do_permission_check: false
        );
        $marker_element = $home_type_element_factory->makeElement(b_do_refresh: false);



        $add_to_home_factory = new SetMemberAdd(params: null,given_set: $this->target_namespace->home_set,is_system: true,calling_namespace: null,
            selected_elements: collect([$marker_element]) );

        $add_to_home_factory->addElements(b_do_refresh: false,b_sticky_override: true);
        $this->params->permission_uuid = Str::uuid();

        ElementValue::writeContextValue(att: $attr, set: $this->target_namespace->home_set, el: $marker_element,value: $this->params->toArray());

        return $this->params;
    }



    /**
     * @throws \Throwable
     */
    public static function makeTree(
        ChangeNamespacesParamData $params,
        ?UserNamespace            $calling_namespace,
        ?UserNamespace            $target_namespace,
        bool                      $do_permission_check = true,
        ?User $target_user           =null,
        ?IThangBuilder            $builder = null
    ) : UserNamespaceData|Thang|IThangBuilder
    {

        if ($do_permission_check && $target_namespace->namespace_user_id !== $calling_namespace->namespace_user_id) {
            throw new HexbatchPermissionException(__("msg.namespace_not_owner",['ref'=>$target_namespace->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_OWNER);
        }


        $node = new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace,
            target_user: $target_user
        );

        $ret_builder = false;
        if ($builder) { $ret_builder = true;}

        $builder?: $builder = ThangBuilder::createBuilder();

        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class]
        );

        if ($do_permission_check && static::PRE_EVENT_CLASS)
        {
            if ($target_user) {
                static::PRE_EVENT_CLASS::callEventTree(given_element: $target_user->default_namespace->private_element,given_set:$target_user->default_namespace->home_set,builder: $builder);
            } else {
                static::PRE_EVENT_CLASS::callEventTree(given_element: $target_namespace->private_element,given_set:$target_namespace->home_set,builder: $builder);
            }

        }


        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();
    }
}
