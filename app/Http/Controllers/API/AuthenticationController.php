<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\CreateNewUser;
use App\Exceptions\HexbatchAuthException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username'=>['required','string','max:30'],
            'password'=>['required','string','min:8']
        ]);

        $user = User::where('username',$request->username)->first();

        if (!$user || !Hash::check($request->password,$user->password) ) {
            throw new HexbatchAuthException(__("auth.failed"),401);
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
        $user = auth()->user();
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
        (new CreateNewUser)->create($request->all());
        return response()->json(["message" => __("auth.registered")], 204);
    }
}
