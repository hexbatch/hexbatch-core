<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\LocationBound;
use App\Models\UserNamespace;

use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Can be given another type to copy the schedule from
 */
#[HexbatchTitle( title: "Destroys a schdule")]
#[HexbatchBlurb( blurb: "Time bounds can removed if not used by any published type")]
#[HexbatchDescription( description:'')]
class DesignLocationDestroy extends DesignLocationCreate
{
    const UUID = 'f6986ecb-de5e-4551-86cf-2cbc855b9780';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_DESTROY;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        DesignLocationCreate::class
    ];




    /**
     * @throws \Throwable
     */
    protected static function deleteLocationBound(UserNamespace $namespace, LocationBound $given_bound) : void
    {
        static::checkIfGivenIsAdmin(given: $namespace,target: $given_bound->location_namespace);

        if ($given_bound->isInUse()) {
            throw new HexbatchNotPossibleException (
                __('msg.bound_in_use',['ref'=>$given_bound->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_IN_USE);
        } else {
            DB::transaction(function () use(&$given_bound){
                $given_bound->delete();
            });
        }

    }

    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $namespace = $command_args['namespace'];
        $bound = $command_args['given_bound']??null;
        static::deleteLocationBound(namespace: $namespace,given_bound: $bound);
        Log::debug("Called design location delete node",['args'=>$command_args]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data:[]);
    }



}

