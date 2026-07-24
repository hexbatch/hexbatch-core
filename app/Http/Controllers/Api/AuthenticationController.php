<?php

namespace App\Http\Controllers\Api;


use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\Data\ErrorData;
use App\Data\ApiParams\Data\User\Params\CreateTokenParamData;
use App\Data\ApiParams\Data\User\Params\LoginParamData;
use App\Data\ApiParams\Data\User\Params\RegistrationParamData;
use App\Data\ApiParams\Data\User\Response\CreateTokenResponseData;
use App\Data\ApiParams\Data\User\Response\LoginResponseData;
use App\Data\ApiParams\Data\User\Response\MeResponseData;
use App\Exceptions\HexbatchAuthException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Evt;
use Carbon\Carbon;
use Hexbatch\Thangs\Data\ThangData;
use Hexbatch\Thangs\Models\Thang;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            new OA\Response( response: 200, description: 'This is you',content: new JsonContent(ref: MeResponseData::class)),
            new OA\Response( response: CodeOf::HTTP_FORBIDDEN, description: 'Not logged in',content: new JsonContent(ref: ErrorData::class))
        ]
    )]
    public function me(Request $request) {
        $user = $request->user();
        $user->loadMissing(
            'default_namespace',
            'default_namespace.namespace_admins',
            'default_namespace.home_set.element_members',
            'default_namespace.home_set.children_sets',
            'default_namespace.public_element',
            'default_namespace.private_element'
        );
        $out = MeResponseData::from(['user'=>$user,'default_namespace'=>$user->default_namespace,'"token_expires_at'=>$user->token_expires_at]);
        return response()->json($out, CodeOf::HTTP_OK);
    }






    #[OA\Post(
        path: '/api/v1/users/login',
        operationId: 'core.users.login',
        description: "Logs in the user, returns a token to use to call the api",
        summary: "User login with name and password",
        requestBody: new OA\RequestBody( required: true,content: new JsonContent(type: LoginParamData::class) ),
        tags: ['user'],
        responses: [
            new OA\Response( response: CodeOf::HTTP_OK, description: 'Login returns a token',content: new JsonContent(ref: LoginResponseData::class)),
            new OA\Response( response: CodeOf::HTTP_UNAUTHORIZED, description: 'Wrong credentials',content: new JsonContent(ref: ErrorData::class))
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $params = LoginParamData::fromRequest($request);
        $user = User::where('username',$params->username)->first();

        if (!$user || !Hash::check($params->password,$user->password) ) {
            throw new HexbatchAuthException(
                __("auth.failed"),
                CodeOf::HTTP_UNAUTHORIZED,
                RefCodes::BAD_LOGIN);
        }
        //todo do api for login after logging in, to dispatch events
        $user->tokens()->delete(); //change later to keep reserved tokens

        $token = $user->createToken($request->username);

        $ret = LoginResponseData::from([
            'message'=> __("auth.success"),
            'auth_token'=> $token->plainTextToken,
            'expiration_date'=> null
        ]);
        return response()->json($ret);
    }


    /**
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/v1/users/register',
        operationId: 'core.users.register',
        description: "Register a new user",
        summary: 'Creates a namespace along with that new user',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: RegistrationParamData::class)),
        tags: ['user','public'],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Registered', content: new JsonContent(ref: MeResponseData::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),
            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue', content: new JsonContent(ref: ErrorData::class)),
            new OA\Response(    response: CodeOf::HTTP_UNPROCESSABLE_ENTITY, description: 'There was an issue') ,
        ]
    )]
    #[ApiEventMarker( Evt\Server\UserRegistered::class)]
    #[ApiEventMarker( Evt\Server\NamespaceCreated::class)]
    #[ApiTypeMarker( Api\User\Register::class)]
    public function register(Request $request): JsonResponse
    {
        $params = RegistrationParamData::fromRequest($request);
        $data_out = Api\User\Register::doRegistration(params: $params,tags: ['api-top']);
        if ($data_out instanceof Thang) {
            $data_out = ThangData::from($data_out);
            $http_code = CodeOf::HTTP_OK;
        }
        else {
            $http_code = CodeOf::HTTP_CREATED;
        }
        return  response()->json($data_out,$http_code);

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
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorData::class))
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
     *  Any JSON set in the body is converted to passthrough data, which is data associated with the logged in token
     */
    #[OA\Post(
        path: '/api/v1/users/auth/create',
        operationId: 'core.users.auth.create',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( description: "Anything passed to the body, except seconds, is considered passthrough data",
            required: false, content: new JsonContent(type: CreateTokenParamData::class)),
        tags: ['user'],

        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Returns a new token set to that lifetime',
                                content: new JsonContent(ref: CreateTokenResponseData::class)),
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorData::class))
        ]
    )]
    public function create_token(Request $request): JsonResponse
    {
        $params = CreateTokenParamData::from($request);

        $expires = null;
        if ($params->seconds > 0) {
            $expires = Carbon::now()->addSeconds($params->seconds);
        }
        /**
         * @var NewAccessToken $token
         */
        $token = $request->user()->createToken($request->request->getString('token_name','default'),['*'],$expires);


        if (count($params->passthrough)) {
            $token_id = $token->accessToken->id;
            $passthrough_json = json_encode($params->passthrough,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            DB::affectingStatement(
                "UPDATE personal_access_tokens SET passthrough = :json_string WHERE id = :id"
                ,['json_string'=>$passthrough_json,'id'=>$token_id]);
        }
        $out = CreateTokenResponseData::from([
            'auth_token'=> $token->plainTextToken,
            'expires_at' => $expires
        ]);
        return response()->json($out, CodeOf::HTTP_CREATED);
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
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorData::class))

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
            new OA\Response( response: CodeOf::HTTP_BAD_REQUEST, description: 'Something happened',content: new JsonContent(ref: ErrorData::class))
        ]
    )]
    public function remove_current_token(Request $request): JsonResponse
    {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $request->user()->currentAccessToken()?->delete();
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
