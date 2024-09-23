<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchCoreException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimeBoundCollection;
use App\Http\Resources\TimeBoundResource;
use App\Http\Resources\TimeBoundSpanResource;
use App\Models\TimeBound;
use App\Models\User;
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
    //todo update, no longer using the user_id, its a property of a rule now, so put into the nested api for the type/att/rules/bounds
//      but bounds should be sharable , so no longer has attribute owner, or a rule owner.
//      Make once, use anywhere the user has edit access to the types

    protected function adminCheck(TimeBound $bound) {
        $user = Utilities::getTypeCastedAuthUser();
        $bound->bound_owner->checkAdminGroup($user->id);
    }

    public function time_bound_get(TimeBound $bound) {
        $this->adminCheck($bound);
        $out = TimeBound::buildTimeBound(id: $bound->id)->first();
        return response()->json(new TimeBoundResource($out,null,2), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function time_bound_ping(TimeBound $bound,string $time_to_ping ) {

        $this->adminCheck($bound);
        $ping_ts = $bound->ping($time_to_ping);
        if ($ping_ts) {
            return response()->json(new TimeBoundSpanResource($bound), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }
        return response()->json(['bound_id'=>$bound->id,'ping_ts'=>$ping_ts], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
    }

    public function time_bound_list(Request $request,?User $user = null) {
        $logged_user = Utilities::getTypeCastedAuthUser();
        if (!$user) {$user = $logged_user;}
        $user->checkAdminGroup($logged_user->id);
        /** @var TimeBound $out */
        $out = TimeBound::buildTimeBound(user_id: $user->id)->cursorPaginate();
        $request->query->set('skip_spans',true);
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
    public function time_bound_edit(TimeBound $bound, Request $request) {
        $this->adminCheck($bound);

        $is_retired = Utilities::boolishToBool($request->request->get('is_retired'));
        $bound_name = $request->request->getString('bound_name');
        $start = $request->request->getString('bound_start');
        $stop = $request->request->getString('bound_stop');
        $bound_cron = $request->request->getString('bound_cron');
        $cron_timezone = $request->request->getString('bound_cron_timezone');
        $period_length = $request->request->getInt('bound_period_length');
        if ($bound_name || $start || $stop || $period_length  || (empty($bound_cron) && $bound->bound_cron)
            || $bound_cron || $cron_timezone
        ) {
            $bound->checkIsInUse();
        }

        $bound->is_retired = $is_retired;
        if ($bound_name) {
            $bound->setName($bound_name,$bound->bound_owner);
        }

        if($start || $stop || $period_length  || (empty($bound_cron) && $bound->bound_cron) || $bound_cron || $cron_timezone) {
            $bound->setTimes(start: $start,stop:$stop,
                            bound_cron: $bound_cron,period_length: $period_length,
                            bound_cron_timezone: $cron_timezone); //saves and processes
        }


        $out = TimeBound::buildTimeBound(id: $bound->id)->first();

        return response()->json(new TimeBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function time_bound_create(Request $request): JsonResponse {

        $bound_name = $request->request->getString('bound_name');
        $start = $request->request->getString('bound_start');
        $stop = $request->request->getString('bound_stop');
        if (!$bound_name || !$stop || !$start) {
            throw new HexbatchCoreException(__("msg.time_bounds_needs_minimum_info"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_NEEDS_MIN_INFO);
        }
        $bound_cron = $request->request->getString('bound_cron');
        $period_length = $request->request->getInt('bound_period_length');
        $cron_timezone = $request->request->getString('bound_cron_timezone');


        $bound = new TimeBound();
        $user = Utilities::getTypeCastedAuthUser();
        $bound->setName($bound_name,$user);

        $bound->user_id = $user->id;

        $bound->setTimes(start: $start,stop:$stop,
            bound_cron: $bound_cron,period_length: $period_length,
            bound_cron_timezone: $cron_timezone); //saves and processes

        $out = TimeBound::buildTimeBound(id: $bound->id)->first();
        return response()->json(new TimeBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
