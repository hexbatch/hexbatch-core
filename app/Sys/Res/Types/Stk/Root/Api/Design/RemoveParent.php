<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Types\Params\TypeParentsParamData;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;

#[ApiParamMarker( param_class: TypeParentsParamData::class)]
class RemoveParent extends Api\DesignApi
{
    const UUID = '93936e61-682b-43ed-a7ca-e6a9c610e242';
    const TYPE_NAME = 'api_design_remove_parent';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignParentRemove::class,
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api remove type parent node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function removeParent(
        UserNamespace $calling_namespace,TypeParentsParamData $params,ElementType $given_type,bool $do_permission_check,
        array $tags = [], ?IThangBuilder $builder = null
    ) : ElementType|Thang
    {

        $parent_type = ElementType::getElementType(uuid: $params->parent_ref_uuid);

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge([static::class],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command)
        ;

        Act\Cmd\Ds\DesignParentRemove::removeParent(calling_namespace: $calling_namespace,given_type: $given_type,
            parent_type: $parent_type,do_permission_check: $do_permission_check, builder: $builder);



        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  ElementType::getElementType(uuid: $data['ref_uuid']);
        } else {
            return $thang;
        }

    }

}

