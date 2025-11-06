<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Locations\Location;
use App\Helpers\Utilities;
use App\Models\LocationBound;
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

#[ApiParamMarker( param_class: Location::class)]
class CreateLocation extends Api\DesignApi implements ICommandCallable
{
    const UUID = '508437a6-6307-4dba-b9f0-8ff14c91f583';
    const TYPE_NAME = 'api_design_location';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationCreate::class,
    ];




    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create location node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /** @throws \Throwable */
    public static function makeLocation(UserNamespace $namespace,Location $params , array $tags = [], ?IThangBuilder $builder = null)
    : LocationBound|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['create-location'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->tree($my_command)
            ->leaf(
                command_class: Act\Cmd\Ds\DesignLocationCreate::class,
                command_args: [
                    'location_params'=>$params->toArray(),
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid
                ],
                command_tags: [Act\Cmd\Ds\DesignLocationCreate::class]
            );

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            /** @var LocationBound $loc_bound|null $time_bound */
            $loc_bound = LocationBound::buildLocationBound(uuid: $data['ref_uuid'],with_namespace: true)->first();
            return $loc_bound;
        } else {
            return $thang;
        }

    }
}

