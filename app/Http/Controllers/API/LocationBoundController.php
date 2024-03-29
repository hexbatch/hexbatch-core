<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchCoreException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationBoundCollection;
use App\Http\Resources\LocationBoundResource;
use App\Models\Enums\Bounds\LocationType;
use App\Models\LocationBound;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class LocationBoundController extends Controller
{
    /**
     * @uses LocationBound::bound_owner()
     */
    protected function adminCheck(LocationBound $bound) {
        $bound->bound_owner->checkAdminGroup(Utilities::getTypeCastedAuthUser()?->id);
    }

    public function location_bound_get(LocationBound $bound) {
        $this->adminCheck($bound);
        $out = LocationBound::buildLocationBound(id: $bound->id)->first();
        return response()->json(new LocationBoundResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @param LocationBound $bound
     * @param string $location_json_to_ping
     * @return JsonResponse
     * @throws ValidationException
     */
    public function location_bound_ping(LocationBound $bound,string $location_json_to_ping ) {

        $this->adminCheck($bound);
        $b_hit = $bound->ping($location_json_to_ping);

        if ($b_hit) {
            $out = LocationBound::buildLocationBound(id: $bound->id)->first();
            return response()->json(new LocationBoundResource($out,null,2), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }
        return response()->json(['bound_id'=>$bound->id,'tested'=>$location_json_to_ping], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
    }

    public function location_bound_list(?User $user = null) {
        $logged_user = Utilities::getTypeCastedAuthUser();
        if (!$user) {$user = $logged_user;}
        $user->checkAdminGroup($logged_user->id);
        /** @var LocationBound $out */
        $out = LocationBound::buildLocationBound(user_id: $user->id)->cursorPaginate();
        return response()->json(new LocationBoundCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function location_bound_delete(LocationBound $bound) {
        $this->adminCheck($bound);
        $bound->checkIsInUse();
        $out = LocationBound::buildLocationBound(id: $bound->id)->first();
        $bound->delete();
        return response()->json(new LocationBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function location_bound_edit(LocationBound $bound, Request $request) {
        $this->adminCheck($bound);


        $is_retired = Utilities::boolishToBool($request->request->get('is_retired'));
        $bound_name = $request->request->getString('bound_name');
        $geo_json = $request->request->getString('geo_json');

        if ($bound_name || $geo_json) {
            $bound->checkIsInUse();
        }

        $user = Utilities::getTypeCastedAuthUser();

        if ($bound_name) {
            $bound->setName($bound_name,$user);
        }

        if ($geo_json) {
            $bound->setShape($geo_json,$bound->location_type);
        }

        $bound->is_retired = $is_retired;
        $bound->save();

        $out = LocationBound::buildLocationBound(id: $bound->id)->first();

        return response()->json(new LocationBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function location_bound_create(Request $request, LocationType $location_type): JsonResponse {

        $bound_name = $request->request->getString('bound_name');
        $geo_json = $request->request->all('geo_json');

        if (!$bound_name || !$geo_json ) {
            throw new HexbatchCoreException(__("msg.location_bounds_needs_minimum_info"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_NEEDS_MIN_INFO);
        }

        $bound = new LocationBound();
        $user = Utilities::getTypeCastedAuthUser();
        $bound->setName($bound_name,$user);

        $bound->user_id = $user->id;
        $bound->setShape(json_encode($geo_json),$location_type);
        $bound->save();

        $out = LocationBound::buildLocationBound(id: $bound->id)->first();
        return response()->json(new LocationBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
