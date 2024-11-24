<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\CreateNewUser;
use App\Api\Users\CreateToken\CreateTokenParams;
use App\Api\Users\CreateToken\CreateTokenResponse;
use App\Api\Users\CreateToken\HexbatchSecondsToLive;
use App\Api\Users\Login\LoginParams;
use App\Api\Users\Login\LoginResponse;
use App\Api\Users\Me\MeResponse;
use App\Api\Users\Registration\RegistrationParams;
use App\Exceptions\HexbatchAuthException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class AuthenticationController extends Controller
{




    #[OA\Get(
        path: '/api/v1/users/me',
        operationId: 'core.users.me',
        description: "Shows the logged in user",
        summary: "This will show the user and default namespace details",
        responses: [
            new OA\Response( response: 200, description: 'This is you',content: new JsonContent(ref: MeResponse::class))
        ]
    )]
    public function me(Request $request) {
        $user = User::buildUser($request->user()->id)->first();

        return response()->json(
            \MattyRad\OpenApi\Serializer::serialize(new MeResponse(user: $user)), CodeOf::HTTP_OK);
    }






    #[OA\Post(
        path: '/api/v1/users/login',
        operationId: 'core.users.login',
        requestBody: new OA\RequestBody( required: true,content: new JsonContent(type: LoginParams::class) ),
        responses: [
            new OA\Response( response: CodeOf::HTTP_OK, description: 'Login returns a token',content: new JsonContent(ref: LoginResponse::class))
        ]
    )]
    public function login(Request $request): JsonResponse
    {

        $params = new LoginParams();
        $params->fromCollection(new Collection($request->all()));
        $user = User::where('username',$params->getUsername())->first();

        if (!$user || !Hash::check($params->getPassword(),$user->password) ) {
            throw new HexbatchAuthException(
                __("auth.failed"),
                CodeOf::HTTP_UNAUTHORIZED,
                RefCodes::BAD_LOGIN);
        }

        $user->tokens()->delete(); //change later to keep reserved tokens

        $token = $user->createToken($request->username)->plainTextToken;
        $serialized = \MattyRad\OpenApi\Serializer::serialize(new LoginResponse(message: __("auth.success"),auth_token: $token));
        return response()->json($serialized);
    }




    #[OA\Post(
        path: '/api/v1/users/register',
        operationId: 'core.users.register',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: RegistrationParams::class)),
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Register with just a username and a password',
                                content: new JsonContent(ref: MeResponse::class))
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        //todo put in db transaction, the user and ns creation and the ns home set stuff
        //  the username and the default namespace need to be the same name (convention, not needed otherwise)
        try {
            $params = new RegistrationParams();
            $params->fromCollection(new Collection($request->all()));
            $user = (new CreateNewUser)->create([
                'username' => $params->getUsername(),'password'=>$params->getPassword(),
                'password_confirmation'=>$params->getPassword()]);

            $user->refresh();
            return response()->json(\MattyRad\OpenApi\Serializer::serialize(new MeResponse(user: $user)), CodeOf::HTTP_CREATED);
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                CodeOf::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BAD_REGISTRATION);
        }
    }


    /**
     * Logs the user out of all tokens (destroys all tokens)
     */
    #[OA\Delete(
        path: '/api/v1/users/logout',
        operationId: 'core.users.logout',
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'All the tokens owned by the user were destroyed')
        ]
    )]
    public function logout(): JsonResponse
    {
        // Get the authenticated user
        $user = Utilities::getTypeCastedAuthUser();
        // revoke the users token
        $user->tokens()->delete();

        return response()->json();
    }


    /**
     * Create a new token with optional lifetime set in seconds
     *
     *  Any json set in the body is converted to passthrough data, which is data associated with the logged in token
     */
    #[OA\Post(
        path: '/api/v1/users/auth/create',
        operationId: 'core.users.auth.create',
        requestBody: new OA\RequestBody(    required: false,
                                            content: new JsonContent(
                                            description: "Anything passed to the body is considered passthrough data",
                                            type: 'object', nullable: true)),

        parameters: [new OA\PathParameter(  name: 'seconds', description: "determines seconds to live, optional",
                                            in: 'path', required: false,  schema: new OA\Schema(ref: HexbatchSecondsToLive::class) )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Returns a new token set to that lifetime',
                                content: new JsonContent(ref: CreateTokenResponse::class))
        ]
    )]
    public function create_token(Request $request,?int $seconds=null): JsonResponse
    {
        $params = new CreateTokenParams();
        $collect = new Collection($request->all());
        $collect['seconds_to_live'] = $seconds;
        $params->fromCollection($collect);

        $expires = null;
        if ($params->getSeconds()) {
            $expires = Carbon::now()->addSeconds($params->getSeconds());
        }
        /**
         * @var NewAccessToken $token
         */
        $token = $request->user()->createToken($request->request->getString('token_name','default'),['*'],$expires);


        if (count($params->getPassthrough())) {
            $token_id = $token->accessToken->id;
            $passthrough_json = json_encode($params->getPassthrough(),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            DB::affectingStatement(
                "UPDATE personal_access_tokens SET passthrough = :json_string WHERE id = :id"
                ,['json_string'=>$passthrough_json,'id'=>$token_id]);
        }
        return response()->json(\MattyRad\OpenApi\Serializer::serialize(new CreateTokenResponse(auth_token: $token->plainTextToken)),
            CodeOf::HTTP_CREATED);
    }




    /**
     * Gets the passthrough data associated with this token used to authenticate this call
     */
    #[OA\Get(
        path: '/api/v1/users/auth/passthrough',
        operationId: 'core.users.auth.passthrough',
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK,  description: 'Gets any immutable passthrough data stored when the token was created',
                content: new JsonContent(type: 'object', nullable: true) )

        ]
    )]
    public function get_token_passthrough(Request $request): JsonResponse
    {
        $json_string = $request->user()->currentAccessToken()->passthrough;
        $h = json_decode($json_string,false);
        if (empty($h)) {
            $h = [];
        }
        return response()->json($h);
    }


    /**
     * Deletes the current token, other tokens not deleted
     *
     * Allows for services to create extra tokens, use them, and then remove them
     */
    #[OA\Delete(
        path: '/api/v1/users/auth/remove_current_token',
        operationId: 'core.users.auth.remove_current_token',
        responses: [
            new OA\Response(    response: CodeOf::HTTP_NO_CONTENT, description: 'Nothing returned')
        ]
    )]
    public function remove_current_token(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([], CodeOf::HTTP_NO_CONTENT);
    }





    public function delete_user(): JsonResponse
    {
        //todo implement delete user, removes this user, deletes the namespaces, including the default
        // check if the StartUserDeletion is on the default ns private element, and if a start for the ns transfer is still there, then make sure that is done first
        return response()->json([], CodeOf::HTTP_SERVICE_UNAVAILABLE);
    }


    public function available(): JsonResponse
    {
        //todo implement available which is given a name looks through both the usernames and the namespaces (with default server), if not found then 200
        return response()->json([], CodeOf::HTTP_SERVICE_UNAVAILABLE);
    }




}
