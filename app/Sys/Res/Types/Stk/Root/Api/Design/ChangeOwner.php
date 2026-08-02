<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiEventMarker;
use App\Data\ApiParams\Data\Types\Params\TypeOwnershipChangeParamData;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;
use App\Sys\Res\Types\Stk\Root\Evt;


#[ApiEventMarker( Evt\Server\TypeOwnerChanging::class)]
#[ApiEventMarker( Evt\Server\TypeOwnerChanged::class)]
class ChangeOwner extends Api\DesignApi implements ICommandCallable
{
    const UUID = '1a222e21-c548-4555-95ad-74aee1387f17';
    const TYPE_NAME = 'api_design_change_owner';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignOwnerChange::class,
    ];




    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        Log::debug("Called api create type node");
        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function changeOwner(
        UserNamespace $calling_namespace,TypeOwnershipChangeParamData $params,ElementType $given_type,bool $do_permission_check,
        array $tags = [], ?IThangBuilder $builder = null
    ) : ElementType|Thang
    {
        if ($params->namespace_uuid === $calling_namespace->ref_uuid) { return $given_type;}

        $given_namespace = UserNamespace::getThisNamespace(uuid: $params->namespace_uuid);
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['change-design-owner'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command)
            ;

        if ($do_permission_check)
        {
            $builder->tree(
                command_class: Evt\Server\TypeOwnerChanged::class,
                command_args: new Evt\Server\TypeOwnerChanged(
                    given_type:$given_type,
                    given_namespace:$given_namespace,
                    old_namespace: $given_type->owner_namespace
                )->toArray(),
                command_tags: [Evt\Server\TypeOwnerChanged::class]
            );
        }


        $builder->tree(
            command_class: Act\Cmd\Ds\DesignOwnerChange::class,
            command_args: new Act\Cmd\Ds\DesignOwnerChange(
                given_type:$given_type,
                given_namespace:$given_namespace,
                caller_namespace: $calling_namespace,
                do_permission_check: $do_permission_check
            )->toArray(),
            command_tags: [Act\Cmd\Ds\DesignOwnerChange::class]
        );

        if ($do_permission_check)
        {
            $builder = Evt\Server\TypeOwnerChanging::makeEventTree(
                given_type:$given_type,
                given_namespace:$given_namespace,
                old_namespace: $given_type->owner_namespace,
                builder: $builder
            );

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

