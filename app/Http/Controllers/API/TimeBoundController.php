<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\RefCodes;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimeBoundResource;
use App\Models\TimeBound;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/*
 | Method | Path                     | Route Name | Operation                                        | Args                                        |
|--------|--------------------------|------------|--------------------------------------------------|---------------------------------------------|
| Delete | bounds/schedule/:id      |            | Deletes an unused schedule                       |                                             |
| Get    | bounds/schedule/:id      |            | shows the time data with maybe list of schedules | optional time range for scheduling          |
| Get    | bounds/schedules/list    |            | Shows a list of all the bounds the user has      | iterator , optional range to show schedules |
| Get    | bounds/schedule/:id/ping |            | returns true or false if a time in bounds        | date time or none for now                   |

 */
class TimeBoundController extends Controller
{
    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function time_bound_create(Request $request): JsonResponse {
        $bound = new TimeBound();
        $bound_name = $request->request->getString('bound_name');
        $bound->setBoundName($bound_name);
        $user = auth()->user();
        $conflict =  TimeBound::where('user_id', $user?->id)->where('bound_name',$bound->bound_name)->first();
        if ($conflict) {
            throw new HexbatchNameConflictException(__("msg.unique_resource_name_per_user",['resource_name'=>$bound->bound_name]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RESOURCE_NAME_UNIQUE_PER_USER);
        }
        $bound->user_id = $user->id;
        $start = $request->request->getString('bound_start');
        $stop = $request->request->getString('bound_stop');
        if (empty($start) || empty($stop)) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_valid_stop_start"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TIME_BOUND_INVALID_START_STOP);
        }

        try {
            $bound_start_ts = Carbon::create($start)->unix();
            $bound_stop_ts = Carbon::create($stop)->unix();
        } catch (InvalidFormatException $ie) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_valid_stop_start"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TIME_BOUND_INVALID_START_STOP,$ie);
        }

        if ($bound_stop_ts < $bound_start_ts) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_valid_stop_start"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TIME_BOUND_INVALID_START_STOP);
        }
        $bound->bound_start = TimeBound::convertTsToSqlTime($bound_start_ts);
        $bound->bound_stop = TimeBound::convertTsToSqlTime($bound_stop_ts);

        $bound->bound_cron = null;
        if ($request->request->has('bound_cron')) {
            $bound->bound_cron = $request->request->getString('bound_cron');
            if (!TimeBound::checkCronString($bound->bound_cron) ) {
                throw new HexbatchNameConflictException(__("msg.time_bounds_invalid_cron_string"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::TIME_BOUND_INVALID_CRON);
            }

            $bound->bound_period_length = $request->request->getInt('bound_period_length');
            if ($bound->bound_period_length < 1) {
                throw new HexbatchCoreException(__("msg.time_bound_period_must_be_with_cron"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::TIME_BOUND_INVALID_PERIOD);
            }
        }


        $bound->save();

        $bound->makeSpansUntil(time() + TimeBound::MAKE_PERIOD_SECONDS);
        $out = TimeBound::buildTimeBound(id: $bound->id);
        return response()->json(new TimeBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
