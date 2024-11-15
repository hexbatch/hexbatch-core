<?php

namespace App\Api\Users\CreateToken;

use App\Api\IApiOaParams;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Http\Request;

/**
 * Any data in the body is set to the passthrough data for the token
 */

class CreateTokenParams implements IApiOaParams
{
    const MAX_PASSTHROUGH_SIZE = 1024*20; //20k

    protected array $passthrough;

    protected ?int $seconds = null;

    public function fromRequest(Request $request,?int $seconds_to_live = null)
    {
        $this->passthrough = $request->all();
        if (mb_strlen(Utilities::maybeEncodeJson($this->passthrough) ) > static::MAX_PASSTHROUGH_SIZE) {
            throw new HexbatchNotPossibleException(__("msg.passthrough_data_too_big",['max'=>static::MAX_PASSTHROUGH_SIZE]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BAD_LOGIN);
        }
        $this->seconds = $seconds_to_live;
        if ($this->seconds > HexbatchSecondsToLive::MAX_SECONDS || $this->seconds < 1) {
            throw new HexbatchNotPossibleException(__("msg.token_too_long_lived",['seconds'=>HexbatchSecondsToLive::MAX_SECONDS]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BAD_LOGIN);
        }
    }

    public function getPassthrough(): array
    {
        return $this->passthrough;
    }

    public function getSeconds(): ?int
    {
        return $this->seconds;
    }



}
