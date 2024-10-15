<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchAuthException;
use App\Exceptions\RefCodes;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        throw new HexbatchAuthException(
            __("auth.failed"),
            \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED,
            RefCodes::BAD_LOGIN);

    }
}
