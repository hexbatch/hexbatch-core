<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Locations\Location;
use App\Enums\Sys\TypeOfAction;
use App\Models\LocationBound;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[HexbatchTitle( title: "Create a location")]
#[HexbatchBlurb( blurb: "Create a 2d map bounds or a 3d shape")]
#[HexbatchDescription( description:'
# create a location bound
* bound_uuid if editing
* bound_name
* location_type
* geo_json
* display

')]
class DesignLocationCreate extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = 'f26dcdcb-09e4-41df-b435-3e7b106c6282';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_CREATE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $params = Location::validateAndCreate($command_args['location_params']);
        $namespace = $command_args['namespace'];
        $bound = $command_args['given_bound']??null;
        $new_location = static::createLocationBound(params: $params,namespace: $namespace,given_bound: $bound);
        Log::debug("Called design location create node",['args'=>$command_args,'location'=>$new_location]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $new_location->toArray());
    }
    /**
     * @throws \Throwable
     */
    protected static function createLocationBound(Location $params, UserNamespace $namespace, ?LocationBound $given_bound = null) : LocationBound
    {
        if ($given_bound) {
            static::checkIfGivenIsAdmin(given: $namespace,target: $given_bound->location_namespace);
        }
        DB::transaction(function () use($params,$namespace,&$given_bound){
            $collect = new Collection(
                [
                    'bound_name' => $params->bound_name,
                    'location_type' => $params->location_type,
                    'geo_json' => $params->geo_json,
                    'display' => $params->display_json
                ]
            );
            if ($given_bound) {
                $given_bound = LocationBound::collectLocationBound(collect: $collect,bound: $given_bound);
            } else {
                $given_bound = LocationBound::collectLocationBound(collect: $collect,namespace: $namespace);
            }
        });
        return $given_bound;
    }
}

