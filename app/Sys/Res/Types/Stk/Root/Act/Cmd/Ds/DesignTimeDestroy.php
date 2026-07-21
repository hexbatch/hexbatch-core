<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;

use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


#[HexbatchTitle( title: "Deletes a schedule")]
#[HexbatchBlurb( blurb: "Schdules can be removed if not used by any published type")]
#[HexbatchDescription( description:'')]
class DesignTimeDestroy extends DesignTimeCreate
{
    const UUID = '1f104a48-34f4-4338-9723-a62fccbbe83a';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TIME_DESTROY;
//
    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];



    /** @throws \Throwable */
    protected static function deleteScheduleBound(UserNamespace $namespace, TimeBound $given_bound) : void
    {
        static::checkIfGivenIsAdmin(given: $namespace,target: $given_bound->schedule_namespace);

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
        static::deleteScheduleBound(namespace: $namespace,given_bound: $bound);
        Log::debug("Called design time delete node",['args'=>$command_args]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data:[]);
    }

}

