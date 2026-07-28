<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Data\ApiParams\Data\Namespaces\Params\DeleteNamespacesParamData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Enums\Sys\TypeOfAction;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSetMember;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\Sys\Res\Atr\Stk\Placeholder\MarkerData;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Marker\DeletionMarker;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class NamespaceDestroy extends Act\Cmd\Ns
{
    const UUID = '0253a9c0-78db-4f8d-b648-7d2abd5ac47c';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_DESTROY;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceDestroyed::class
    ];


    public function __construct(
        protected DeleteNamespacesParamData $params,
        protected UserNamespace           $target_namespace,
        protected UserNamespace           $calling_namespace,
        protected bool           $do_permission_check = true,
    )
    {

    }


    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'target_namespace'=> $this->target_namespace,
            'calling_namespace'=> $this->calling_namespace,
            'do_permission_check'=> $this->do_permission_check,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = null;
        if (!empty($args['params']??null)) {
            $params = DeleteNamespacesParamData::makingUsingCodeArray($args['params']);
        }
        $target_namespace = static::getNamespaceFromArray('target_namespace',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $do_permission_check = (bool)$args['do_permission_check'];

        return new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace,
            do_permission_check: $do_permission_check
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
            $info = $work->doTheDeletion();

            $r = new Evt\Server\NamespaceDestroyed(given_type: $work->target_namespace->owner_user->default_namespace->namespace_base_type,
                given_uuid: $work->target_namespace->ref_uuid);

            $r->callTreeByItself($children_args);
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL, data: $info);
    }

    /**
     * @throws \Throwable
     * does not reload ns after attaching stuff together
     */
    public function doTheDeletion(

    ) : array
    {
        //see if element is in the target home ns, and that it has the same uuid

        $attr = Attribute::getThisAttribute(uuid: MarkerData::getAttributeUuid());

        $sys_element_ids = ElementSetMember::where('holder_set_id',$this->target_namespace->namespace_home_set_id)->join('elements as e',
            /** @param JoinClause $join */
            function (JoinClause $join)  {
                $join->on('element_set_members.member_element_id', '=', 'e.id')
                    ->where('e.is_system',true);
            }
        )->pluck('member_element_id')->toArray();

        $list = ElementValue::readValues(set_id: $this->target_namespace->namespace_home_set_id,element_ids: $sys_element_ids,attribute_ids: [$attr->id],
            caller_namespace_id: $this->calling_namespace->id);

        if ($this->do_permission_check)
        {
            $found_params = null;
            foreach ($list->data as $datum) {
                if ($datum->type_uuid !== DeletionMarker::getTypeUuid()) { continue;}
                $params = DeleteNamespacesParamData::makingUsingCodeArray($datum->data);
                if ($params->permission_uuid === $this->params->permission_uuid) {
                    $found_params = $params;
                }
            }

            if (!$found_params) {
                throw new HexbatchPermissionException(__("msg.namespace_cannot_delete_when_no_precursor",
                    ['ref'=>$this->target_namespace->getName(),'uuid'=>$this->params->permission_uuid]),
                    Response::HTTP_FORBIDDEN,
                    RefCodes::NAMESPACE_CANNOT_DELETE_WITHOUT_UUID);
            }
        }


        DB::transaction(function (){
            if ($this->params->transfer_elements_to_default) {
                Element::where('element_namespace_id',$this->target_namespace->id)
                    ->update(['element_namespace_id' => $this->target_namespace->owner_user->default_namespace->id]);
            }

            if ($this->params->transfer_types_to_default) {
                ElementType::where('owner_namespace_id',$this->target_namespace->id)
                    ->update(['owner_namespace_id' => $this->target_namespace->owner_user->default_namespace->id]);
            }
            $this->target_namespace->delete();
        });

        $ret = [];

        return $ret;
    }



    /**
     * @throws \Throwable
     */
    public static function doDeletion(
        DeleteNamespacesParamData $params,
        ?UserNamespace           $calling_namespace,
        ?UserNamespace           $target_namespace,
        bool $do_permission_check = true,
        ?IThangBuilder $builder = null
    ) : UserNamespaceData|Thang|IThangBuilder
    {

        if ($do_permission_check) {

            if ($target_namespace->namespace_user_id !== $calling_namespace->namespace_user_id) {
                throw new HexbatchPermissionException(__("msg.namespace_not_owner",['ref'=>$target_namespace->getName()]),
                    Response::HTTP_FORBIDDEN,
                    RefCodes::NAMESPACE_NOT_OWNER);
            }

        }

        //check for default ns
        $target_namespace->loadMissing('owner_user');
        if ($target_namespace->owner_user->default_namespace_id === $target_namespace->namespace_user_id) {
            throw new HexbatchPermissionException(__("msg.namespace_cannot_delete_default",
                ['ref'=>$target_namespace->getName(),'user_name'=>$target_namespace->owner_user->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_CANNOT_DELETE_CORE_PARTS);
        }


        $rem_ns =  UserNamespaceData::from($target_namespace);

        $node = new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace,
            do_permission_check: $do_permission_check
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
            return  $rem_ns;
        } else {
            return $thang;
        }
    }

}

