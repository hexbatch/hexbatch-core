<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\DeleteNamespacesParamData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Models\Phase;
use App\Models\UserNamespace;
use App\Sys\Res\Atr\Stk\Placeholder\MarkerData;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Marker\DeletionMarker;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


#[ApiParamMarker( param_class: DeleteNamespacesParamData::class)]
class NamespaceDeletionStart extends Act\Cmd\Ns implements ICommandCallable
{
    const UUID = 'efb8d969-20a6-4bd7-9f43-4e0448338931';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_PREP_DELETION;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [];


    public function __construct(
        protected DeleteNamespacesParamData $params,
        protected UserNamespace           $target_namespace,
        protected UserNamespace           $calling_namespace,
    )
    {

    }


    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'target_namespace'=> $this->target_namespace,
            'calling_namespace'=> $this->calling_namespace,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = null;
        if (!empty($args['params']??null)) {
            $params = DeleteNamespacesParamData::makingUsingCodeArray($args['params']);
        }
        $target_namespace = static::getNamespaceFromArray('target_namespace',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);

        return new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace
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
            $info = $work->startDeletion();
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: $info->toArray());
    }

    /**
     * @throws \Throwable
     * does not reload ns after attaching stuff together
     */
    public function startDeletion(

    ) : DeleteNamespacesParamData
    {
        $marker = ElementType::getElementType(uuid: DeletionMarker::getTypeUuid());
        $phase = Phase::getDefaultPhase();
        $attr = Attribute::getThisAttribute(uuid: MarkerData::getAttributeUuid());
        $home_type_element_factory = new Act\Cmd\Ty\ElementCreate(element_type: $marker, phase: $phase, number_to_create: 1,
            owner_namespace: $this->target_namespace, is_system: true, calling_namespace: $this->calling_namespace, do_permission_check: false
        );
        $marker_element = $home_type_element_factory->makeElement(b_do_refresh: false);



        $add_to_home_factory = new Act\Cmd\St\SetMemberAdd(params: null,given_set: $this->target_namespace->home_set,is_system: true,calling_namespace: null,
            selected_elements: collect([$marker_element]) );

        $add_to_home_factory->addElements(b_do_refresh: false,b_sticky_override: true);
        $this->params->permission_uuid = Str::uuid();

        ElementValue::writeContextValue(att: $attr, set: $this->target_namespace->home_set, el: $marker_element,value: $this->params->toArray());

        return $this->params;
    }



    /**
     * @throws \Throwable
     */
    public static function doStartDeletion(
        DeleteNamespacesParamData $params,
         ?UserNamespace           $calling_namespace,
         ?UserNamespace           $target_namespace,
         bool $do_permission_check = true,
         ?IThangBuilder $builder = null
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
            calling_namespace: $calling_namespace
        );

        $ret_builder = false;
        if ($builder) { $ret_builder = true;}

        $builder?: $builder = ThangBuilder::createBuilder();

        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class]
        );


        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  UserNamespaceData::from($data['namespace']);
        } else {
            return $thang;
        }
    }


}

