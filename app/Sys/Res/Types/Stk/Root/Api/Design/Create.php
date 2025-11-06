<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Types\Params\TypeParamData;
use App\Models\ElementType;
use App\Models\Server;
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

#[ApiParamMarker( param_class: TypeParamData::class)]
class Create extends Api\DesignApi implements ICommandCallable
{
    const UUID = 'eb4805a7-3f25-4aae-a42b-4308edc66185';
    const TYPE_NAME = 'api_design_create';

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignCreate::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create type node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function createDesign(UserNamespace      $namespace, TypeParamData $params ,
                                        bool $is_system = false,
                                        array $tags = [], ?IThangBuilder $builder = null)
    : ElementType|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['create-design'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->tree($my_command)
            ->leaf(
                command_class: Act\Cmd\Ds\DesignCreate::class,
                command_args: new Act\Cmd\Ds\DesignCreate(
                    params:$params,
                    is_system:$is_system,
                    use_ref: null,
                    owner_namespace: $namespace,
                    server: Server::getDefaultServer()
                )->toArray(),
                command_tags: [Act\Cmd\Ds\DesignCreate::class]
            );

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  ElementType::getElementType(uuid: $data['ref_uuid']);
        } else {
            return $thang;
        }

    }

}

