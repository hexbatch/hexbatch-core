<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Live\Params\LivePermissionParamData;
use App\Enums\Sys\TypeOfAction;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Models\ElementType;
use App\Models\LivePermission;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;

class LivePermissionRemove extends Act\Cmd\Ty
{
    const UUID = 'ff695e94-24cb-400b-ab9b-3ca641af1c7a';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_PERMISSION_REMOVE;


    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\LivePermissionRemoving::class,
        Evt\Server\LivePermissionRemoved::class,
    ];

    #[ApiParamMarker( param_class: LivePermissionParamData::class)]
    public function __construct(
        protected LivePermissionParamData $params,
        protected UserNamespace $caller_namespace,
        protected bool          $do_permission_check,
        protected ?ElementType $trigger_type = null,
        protected ?ElementType $target_type = null,
        protected ?LivePermission $permission = null,

    )
    {
        if (!$this->trigger_type) {
            $this->trigger_type = ElementType::resolveType(value: $this->params->type_trigger,b_allow_null: false,b_server_relations: true);
        }
        if (!$this->target_type) {
            $this->target_type = ElementType::resolveType(value: $this->params->type_target,b_allow_null: false,b_server_relations: true);
        }

        if ($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $this->target_type->owner_namespace);
        }

        if(!$this->permission) {
            $this->permission = LivePermission::where('live_permission_trigger_type_id',$this->trigger_type->id)
                ->where('live_permission_target_type_id',$this->target_type->id)->first();
            if (!$this->permission) {
                throw new HexbatchNotFound(
                    __('msg.permission_not_found',['target'=>$this->params->type_target,'trigger' => $this->params->type_trigger]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::LIVE_PERMISSION_NOT_FOUND
                );
            }
        }

    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'trigger_type'=>$this->trigger_type,
            'target_type'=>$this->target_type,
            'caller_namespace'=>$this->caller_namespace,
            'do_permission_check'=>$this->do_permission_check,
        ];
    }

    protected static function fromArray(array $args) : static {

        $params = LivePermissionParamData::from($args['params']);
        $trigger_type = static::getTypeFromArray('trigger_type',$args);
        $target_type = static::getTypeFromArray('target_type',$args);
        $caller_namespace =  static::getNamespaceFromArray('caller_namespace',$args,false) ;
        $do_permission_check = (bool)$args['do_permission_check'];
        return new static(params: $params, caller_namespace: $caller_namespace,
            do_permission_check: $do_permission_check, trigger_type: $trigger_type, target_type: $target_type);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $arr = $work->doRemovePermission();

            $r = new Evt\Server\LivePermissionRemoved(given_type: $work->trigger_type,
                other_type: $work->target_type, given_namespace: $work->caller_namespace);
            $r->callTreeByItself($children_args);
        } else {
            $arr = null;
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: ['results'=>$arr]);
    }


    /**
     * @throws \Throwable
     */
    public  function doRemovePermission() : LivePermission {


        $this->permission->delete();
       return $this->permission;
    }


    /**
     * @throws \Throwable
     */
    public static function removePermissionTree(
        LivePermissionParamData $params,
        UserNamespace $calling_namespace,
        bool $do_permission_check,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {


        $me = new static(
            params:$params,
            caller_namespace: $calling_namespace,
            do_permission_check: true
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
            command_priority: -1
        );

        if ($do_permission_check)
        {
            Evt\Server\LivePermissionRemoving::makeEventTree(given_type: $me->trigger_type,
                other_type: $me->target_type, given_namespace: $calling_namespace, builder: $builder);
        }

        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        return $thang;
    }

}

