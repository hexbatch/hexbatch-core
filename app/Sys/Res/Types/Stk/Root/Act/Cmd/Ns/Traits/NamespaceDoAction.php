<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\Traits;

use App\Data\ApiParams\Data\Namespaces\Params\ChangeNamespacesParamData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\ElementSetMember;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Models\User;
use App\Models\UserNamespace;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Database\Query\JoinClause;
use Symfony\Component\HttpFoundation\Response;

trait NamespaceDoAction
{
    public function __construct(
        protected ChangeNamespacesParamData $params,
        protected UserNamespace             $target_namespace,
        protected UserNamespace             $calling_namespace,
        protected ?User                     $target_user = null,
        protected bool                      $do_permission_check = true,
    )
    {

    }


    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'target_namespace'=> $this->target_namespace,
            'calling_namespace'=> $this->calling_namespace,
            'do_permission_check'=> $this->do_permission_check,
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
        $do_permission_check = (bool)$args['do_permission_check'];
        $target_user = static::getUserFromArray('target_user',$args, false);

        return new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace,
            target_user: $target_user,
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
            $info = $work->doAction();
            if (static::POST_EVENT_CLASS) {
                static::POST_EVENT_CLASS::callEventTreeByItself(
                    children_args: $children_args,
                    given_type: $work->getInnerType(),
                    given_uuid: $work->getInnerUuid()
                );
            }
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL, data: $info);
    }

    protected abstract function getInnerUuid() : ?string ;

    protected abstract function getInnerType() : ?ElementType ;

    /**
     * @throws \Throwable
     */
    protected abstract function doInnerAction() ;

    /**
     * @throws \Throwable
     * does not reload ns after attaching stuff together
     */
    public function doAction(

    ) : array
    {
        //see if element is in the target home ns, and that it has the same uuid

        $attr = Attribute::getThisAttribute(uuid: static::ATTRIBUTE_CLASS::getAttributeUuid());

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
                if ($datum->type_uuid !== static::MARKER_CLASS::getTypeUuid()) { continue;}
                $params = ChangeNamespacesParamData::makingUsingCodeArray($datum->data);
                if ($params->permission_uuid === $this->params->permission_uuid) {
                    $found_params = $params;
                }
            }

            if (!$found_params) {
                throw new HexbatchPermissionException(__("msg.operation_cannot_delete_when_no_precursor",
                    ['ref'=>$this->target_namespace->getName(),'uuid'=>$this->params->permission_uuid]),
                    Response::HTTP_FORBIDDEN,
                    RefCodes::NAMESPACE_CANNOT_DELETE_WITHOUT_UUID);
            }
        }


        $this->doInnerAction();

        $ret = [];

        return $ret;
    }



    /**
     * @throws \Throwable
     */
    public static function makeTree(
        ChangeNamespacesParamData $params,
        ?UserNamespace            $calling_namespace,
        ?UserNamespace            $target_namespace,
        ?User                     $target_user = null ,
        bool                      $do_permission_check = true,
        ?IThangBuilder            $builder = null
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
            target_user: $target_user,
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
