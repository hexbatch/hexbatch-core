<?php

namespace App\Api;


use Illuminate\Http\Request;

/**
 * Marks a class which does the json input definition for the OA for success
 */
interface IApiOaParams
{
    public function fromRequest(Request $request);
}
