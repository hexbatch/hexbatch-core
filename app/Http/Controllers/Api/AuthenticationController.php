<?php

namespace App\Http\Controllers\Api;


use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Exceptions\HexbatchAuthException;
use App\Exceptions\HexbatchCodeRollbackException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\OpenApi\ErrorResponse;
use App\OpenApi\Params\Actioning\Registration\RegistrationParams;
use App\OpenApi\Params\Users\CreateTokenParams;
use App\OpenApi\Params\Users\LoginParams;
use App\OpenApi\Results\Callbacks\HexbatchCallbackCollectionResponse;
use App\OpenApi\Results\Users\CreateTokenResponse;
use App\OpenApi\Results\Users\LoginResponse;
use App\OpenApi\Results\Users\MeResponse;
use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Evt;
use Carbon\Carbon;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        security: [['bearerAuth' => []]],
        tags: ['user'],
        responses: [
            new OA\Response( response: 200, description: 'This is you',content: new JsonContent(ref: MeResponse::class)),
            new OA\Response( response: CodeOf::HTTP_FORBIDDEN, description: 'Not logged in',content: new JsonContent(ref: ErrorResponse::class))
        ]
    )]
    public function me(Request $request) {
        $user = User::buildUser($request->user()->id)->first();

        return response()->json(new MeResponse(user: $user,show_namespace: true), CodeOf::HTTP_OK);
    }






    #[OA\Post(
        path: '/api/v1/users/login',
        operationId: 'core.users.login',
        description: "Logs in the user, returns a token to use to call the api",
        summary: "User login with name and password",
        requestBody: new OA\RequestBody( required: true,content: new JsonContent(type: LoginParams::class) ),
        tags: ['user'],
        responses: [
            new OA\Response( response: CodeOf::HTTP_OK, description: 'Login returns a token',content: new JsonContent(ref: LoginResponse::class)),
            new OA\Response( response: CodeOf::HTTP_UNAUTHORIZED, description: 'Wrong credentials',content: new JsonContent(ref: ErrorResponse::class))
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
        return response()->json(new LoginResponse(message: __("auth.success"),auth_token: $token));
    }


    /**
     * @throws \Exception
     */
    #[OA\Post(
        path: '/api/v1/users/register',
        operationId: 'core.users.register',
        description: "Register a new user",
        summary: 'Creates a namespace along with that new user',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: RegistrationParams::class)),
        tags: ['user','public'],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Registered', content: new JsonContent(ref: MeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but unexpected callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Server\UserRegistrationProcessing::class)]
    #[ApiTypeMarker( Api\User\UserRegister::class)]
    public function register(Request $request): JsonResponse
    {

        $what = null;
        try {
            DB::beginTransaction();
            $params = new RegistrationParams();
            $params->fromCollection(new Collection($request->all()));
            $api = new Api\User\UserRegister(is_async: false, params: $params, tags: [ 'api-top']);
            $thing = $api->createThingTree(tags: ['registration']);
            Utilities::ignoreVar($thing);
            $data_out = $api->getCallbackResponse($http_code);
            if ($http_code > 299) {throw new HexbatchCodeRollbackException("Registration Failed");}
            $what =  response()->json(['response' => $data_out], $http_code);
            DB::commit();
            return $what;
        }
        catch (HexbatchCodeRollbackException) {
            DB::rollBack();
            return $what;
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }


    /**
     * Logs the user out of all tokens (destroys all tokens)
     */
    #[OA\Delete(
        path: '/api/v1/users/logout',
        operationId: 'core.users.logout',
        security: [['bearerAuth' => []]],
        tags: ['user'],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'All the tokens owned by the user were destroyed'),
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorResponse::class))
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
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( description: "Anything passed to the body, except seconds, is considered passthrough data",
            required: false, content: new JsonContent(type: CreateTokenParams::class)),
        tags: ['user'],

        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Returns a new token set to that lifetime',
                                content: new JsonContent(ref: CreateTokenResponse::class)),
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorResponse::class))
        ]
    )]
    public function create_token(Request $request): JsonResponse
    {
        $params = new CreateTokenParams();
        $params->fromCollection(new Collection($request->all()));

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
        return response()->json(new CreateTokenResponse(auth_token: $token->plainTextToken), CodeOf::HTTP_CREATED);
    }




    /**
     * Gets the passthrough data associated with this token used to authenticate this call
     */
    #[OA\Get(
        path: '/api/v1/users/auth/passthrough',
        operationId: 'core.users.auth.passthrough',
        security: [['bearerAuth' => []]],
        tags: ['user'],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK,  description: 'Gets any immutable passthrough data stored when the token was created',
                content: new JsonContent(type: 'object', nullable: true) ),
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorResponse::class))

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
        security: [['bearerAuth' => []]],
        tags: ['user'],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_NO_CONTENT, description: 'Nothing returned'),
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorResponse::class))
        ]
    )]
    public function remove_current_token(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([], CodeOf::HTTP_NO_CONTENT);
    }




    #[OA\Delete(
        path: '/api/v1/users/auth/start_deletion',
        operationId: 'core.users.auth.start_deletion',
        description: "The user is deleted. Event can stop this ",
        summary: 'The user deletes the account',
        security: [['bearerAuth' => []]],
        tags: ['user'],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\UserDeletionStarting::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::USER)]
    #[ApiTypeMarker( Api\User\StartUserDeletion::class)]
    public function start_user_deletion() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/users/auth/prepare_deletion',
        operationId: 'core.users.auth.prepare_deletion',
        description: "The user is marked to allow deletion. Event can stop this. Not deleted yet. ",
        summary: 'The user gives permission for its own deletion',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\UserDeletionPreparing::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::USER)]
    #[ApiTypeMarker( Api\User\PrepareUserDeletion::class)]
    public function prepare_user_deletion() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/users/available',
        operationId: 'core.users.available',
        description: "Looks through both the usernames and the namespaces",
        summary: 'Checks if a username can be signed up with',
        tags: ['user','public'],
        responses: [

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Results about the name query',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function available(): JsonResponse
    {
        //todo implement available which is given a name looks through both the usernames and the namespaces (with default server), if not found then 200
        return response()->json([], CodeOf::HTTP_SERVICE_UNAVAILABLE);
    }




}
