<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\RefCodes;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimeBoundCollection;
use App\Http\Resources\TimeBoundResource;
use App\Models\TimeBound;
use App\Models\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/*
 | Method | Path                     | Route Name | Operation                                        | Args                                        |
|--------|--------------------------|------------|--------------------------------------------------|---------------------------------------------|
| Get    | bounds/schedule/:id/ping |            | returns true or false if a time in bounds        | date time or none for now                   |

 */
class TimeBoundController extends Controller
{
    /**
     * @uses TimeBound::bound_owner()
     */
    protected function adminCheck(TimeBound $bound) {
        $user = auth()->user();
        $bound->bound_owner->checkAdminGroup($user->id);
    }

    public function time_bound_get(TimeBound $bound) {
        $this->adminCheck($bound);
        $out = TimeBound::buildTimeBound(id: $bound->id)->first();
        return response()->json(new TimeBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function time_bound_list(?User $user = null) {
        $logged_user = auth()->user();
        if (!$user) {$user = $logged_user;}
        $user->checkAdminGroup($logged_user->id);
        $out = TimeBound::buildTimeBound(user_id: $user->id)->cursorPaginate();
        return response()->json(new TimeBoundCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function time_bound_delete(TimeBound $bound) {
        $this->adminCheck($bound);
        $bound->checkIsInUse();
        $out = TimeBound::buildTimeBound(id: $bound->id)->first();
        $bound->delete();
        return response()->json(new TimeBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
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
            $bound->setCronString($request->request->getString('bound_cron')) ;
            $bound->setPeriodLength($request->request->getInt('bound_period_length'));
        }


        $bound->save();

        $bound->makeSpansUntil(time() + TimeBound::MAKE_PERIOD_SECONDS);
        $out = TimeBound::buildTimeBound(id: $bound->id)->first();
        return response()->json(new TimeBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
