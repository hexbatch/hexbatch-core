<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiEventMarker;
use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Attributes\Params\AttributeParamData;
use App\Models\Attribute;
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
class EditAttribute extends Api\DesignApi implements ICommandCallable
{
    const UUID = '40a60d68-5fb3-472d-9c90-bc033501ab1b';
    const TYPE_NAME = 'api_design_edit_attribute';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeEdit::class,
    ];





    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api edit attribute node");

        $b_approved = true;
        if (count($children_args)) {
            $b_approved = $children_args[static::CHILD_DECISION_KEY]??false;
        }

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,data: [static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function editAttribute(UserNamespace      $calling_namespace, Attribute $given_attribute,
                                         AttributeParamData $params , array $tags = [], ?IThangBuilder $builder = null)
    : Attribute|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['edit-attribute'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ds\DesignAttributeEdit::makeEditAttributeTree(
            params: $params,given_attribute: $given_attribute,
            calling_namespace: $calling_namespace,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  Attribute::getThisAttribute(uuid: $data['ref_uuid'],b_do_relations: true);
        } else {
            return $thang;
        }

    }
}

