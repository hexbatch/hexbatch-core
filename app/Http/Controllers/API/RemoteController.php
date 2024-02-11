<?php

namespace App\Http\Controllers\API;

use App\Helpers\Remotes\Build\DataGathering;
use App\Helpers\Remotes\Build\RemoteAlwaysCanSetOptions;
use App\Helpers\Remotes\Build\RemoteUriGathering;
use App\Http\Controllers\Controller;
use App\Http\Resources\RemoteCollection;
use App\Http\Resources\RemoteResource;
use App\Models\Remote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


/*
| Method | Path                | Route Name | Operation                                    | Args                                            |
|--------|---------------------|------------|----------------------------------------------|-------------------------------------------------|
| Post   | remote              |            | Makes a new remote                           | Required name: optional states, required remote |
| Patch  | remote/:id/edit/    |            | Edit part of value, if possible, sparse      | Any detail , sparse update                      |
| Get    | remote/:id          |            | returns full remote info                     | can pass in optional type and element           |
| Get    | remotes/list        |            | searches for remotes                         | iterator,can pass in filtering info             |
| Get    | remote/:id/test     |            | Sends to the Remote, returns value or issues | add json body for the values it draws on        |
| Delete | remote/:id          |            | Delete Remote, if the user can               |                                                 |                                               |
 */

class RemoteController extends Controller
{
    /**
     * @uses Remote::remote_owner()
     */
    protected function adminCheck(Remote $att) {
        $user = auth()->user();
        $att->remote_owner->checkAdminGroup($user->id);
    }

    public function remote_get(Remote $remote,?string $full = null) {
        $this->adminCheck($remote);
        $out = Remote::buildRemote(id: $remote->id)->first();
        $n_level = (int)$full;
        if ($n_level <= 0) { $n_level =1;}
        return response()->json(new RemoteResource($out,null,$n_level), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @param Remote $remote
     * @return JsonResponse
     */
    public function remote_test(Request $request, Remote $remote) {

        $remote->runRemote($request->collect());
        $out = Remote::buildRemote(id: $remote->id)->first();
        return response()->json(new RemoteResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function remote_list(?User $user = null) {

        $logged_user = auth()->user();
        if (!$user) {$user = $logged_user;}
        $out = Remote::buildRemote(usage_user_id: $user->id)->cursorPaginate();
        return response()->json(new RemoteCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function remote_delete(Remote $remote) {
        $this->adminCheck($remote);
        $remote->checkIsInUse();
        $out = Remote::buildRemote(id: $remote->id)->first();
        $remote->delete();
        return response()->json(new RemoteResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @param Remote $remote
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function remote_edit_patch(Remote $remote, Request $request) {
        $this->adminCheck($remote);

        try {
            DB::beginTransaction();
            // if this is in use then can only edit retired, meta
            // otherwise can edit all but the ownership
            if ($remote->isInUse()) {
                $this->updateInUseRemote($remote,$request);
            } else {
                $user = auth()->user();
                $some_name = $request->request->getString('remote_name');

                if ($some_name && $some_name !== $remote->remote_name) {
                    $remote->setName($request->request->getString('remote_name'),$user);
                }

                $this->updateAllRemote($remote,$request);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }



        $out = Remote::buildRemote(id: $remote->id)->first();

        return response()->json(new RemoteResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    protected function updateAllRemote(Remote $remote, Request $request) {

        (new RemoteUriGathering($request) )->assign($remote);

        $this->updateInUseRemote($remote,$request);

        (new DataGathering($request) )->assign($remote);

    }

    protected function updateInUseRemote(Remote $remote, Request $request) {
        (new RemoteAlwaysCanSetOptions($request) )->assign($remote);
        //saved at this point

    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function remote_create(Request $request): JsonResponse {


        try {
            DB::beginTransaction();
            // if this is in use then can only edit retired, meta
            // otherwise can edit all but the ownership
            $remote = new Remote();
            $user = auth()->user();
            $remote->setName($request->request->getString('remote_name'),$user);
            $remote->user_id = $user->id;
            $this->updateAllRemote($remote,$request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $out = Remote::buildRemote(id: $remote->id)->first();
        return response()->json(new RemoteResource($out,null,2), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
