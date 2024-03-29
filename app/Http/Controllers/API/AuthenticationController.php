<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\CreateNewUser;
use App\Exceptions\HexbatchAuthException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class AuthenticationController extends Controller
{

    public function me(Request $request) {
        $user = User::buildUser($request->user()->id)->first();
        return response()->json(new UserResource($user,null,2), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username'=>['required','string','max:61'],
            'password'=>['required','string','min:8']
        ]);

        $user = User::where('username',$request->username)->first();

        if (!$user || !Hash::check($request->password,$user->password) ) {
            throw new HexbatchAuthException(
                __("auth.failed"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED,
                RefCodes::BAD_LOGIN);
        }

        $user->tokens()->delete(); //change later to keep reserved tokens

        $token = $user->createToken($request->username)->plainTextToken;

        return response()->json([
            "message"=> __("auth.success"),
            "authToken" => $token
        ]);
    }

    public function logout(): JsonResponse
    {
        // Get the authenticated user
        $user = Utilities::getTypeCastedAuthUser();
        // revoke the users token
        $user->tokens()->delete();

        return response()->json([
            "message" => __("auth.logged_out"),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $user = (new CreateNewUser)->create($request->all());
        $user->initUser();
        $user->refresh();
        return response()->json(new UserResource($user,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }


    public function create_token(Request $request,?int $seconds=null): JsonResponse
    {
        $expires = null;
        if ($seconds && $seconds > 0) {
            $expires = Carbon::now()->addSeconds($seconds);
        }
        /**
         * @var NewAccessToken $token
         */
        $token = $request->user()->createToken($request->request->getString('token_name','default'),['*'],$expires);
        $passthrough = $request->all();
        if (count($passthrough)) {
            $token_id = $token->accessToken->id;
            $passthrough_json = json_encode($passthrough,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            DB::affectingStatement(
                "UPDATE personal_access_tokens SET passthrough = :json_string WHERE id = :id"
                ,['json_string'=>$passthrough_json,'id'=>$token_id]);
        }

        return response()->json([
            "authToken" => $token->plainTextToken
        ]);

    }

    public function get_token_passthrough(Request $request): JsonResponse
    {
        $json_string = $request->user()->currentAccessToken()->passthrough;
        $h = json_decode($json_string,false);
        if (empty($h)) {
            $h = [];
        }
        return response()->json($h);
    }

    public function delete_this_token(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }


}
