<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username'=>['required','string','max:30'],
            'password'=>['required','string','min:8']
        ]);

        $user = User::where('username',$request->username)->first();

        if (is_null($user) ) {
            abort(404,"You don't have an account with that username");
        }

        $user->tokens()->delete(); //change later to keep reserved tokens

        if (!Hash::check($request->password,$user->password) ) {
            throw ValidationException::withMessages(['email'=>['The provided credentials are incorrect'],]);
        }

        $token = $user->createToken($request->username)->plainTextToken;

        return response()->json([
            "message"=> "Login Successful",
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
            "message" => "Logged out successfully"
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $user = (new CreateNewUser)->create($request->all());
        return response()->json(['user'=> $user->toArray()], 204);
    }
}
