<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


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
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;

class EditLocation extends CreateLocation
{
    const UUID = '092ffcc2-8feb-4922-9325-fcb3d197886d';
    const TYPE_NAME = 'api_design_edit_location';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationEdit::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api edit time node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /** @throws \Throwable */
    public static function editLocation(UserNamespace $namespace,LocationBound $bound,?Location $params = null, array $tags = [], ?IThangBuilder $builder = null)
    : LocationBound|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['edit-location'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->setSharedArg('given_bound',$bound)
            ->tree($my_command)
            ->leaf([
                'command_class' =>Act\Cmd\Ds\DesignLocationEdit::class,
                'command_args' =>[
                    'location_params'=>$params?->toArray(),
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid
                ],
                'command_tags' =>[Act\Cmd\Ds\DesignLocationEdit::class]
            ]);

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            /** @var LocationBound|null $location_bound */
            $location_bound = LocationBound::buildLocationBound(uuid: $data['ref_uuid'])->first();
            return $location_bound;
        } else {
            return $thang;
        }

    }

}

