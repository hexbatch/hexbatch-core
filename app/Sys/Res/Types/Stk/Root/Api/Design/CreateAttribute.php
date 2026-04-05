<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiEventMarker;
use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Attributes\Params\AttributeParamData;
use App\Models\Attribute;
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

#[ApiParamMarker( param_class: AttributeParamData::class)]
#[ApiEventMarker( Evt\Server\AttributePending::class)]
class CreateAttribute extends Api\DesignApi implements ICommandCallable
{
    const UUID = '745c1851-68af-4420-b6f9-037aa63bebc7';
    const TYPE_NAME = 'api_design_create_attribute';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeCreate::class,
    ];






    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create attribute node");

        $b_approved = true;
        if (count($children_args)) {
            $b_approved = $children_args[static::CHILD_DECISION_KEY]??false;
        }

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function createAttribute(UserNamespace      $calling_namespace, ElementType $given_type,
                                           bool               $is_system, ?string              $use_ref,
                                           AttributeParamData $params , array $tags = [], ?IThangBuilder $builder = null)
    : Attribute|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['create-attribute'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ds\DesignAttributeCreate::makeCreateAttributeTree(
            params: $params,is_system: $is_system,
            use_ref: $use_ref,calling_namespace: $calling_namespace,given_type: $given_type,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  Attribute::getThisAttribute(uuid: $data['ref_uuid'],b_do_relations: true);
        } else {
            return $thang;
        }

    }

}

