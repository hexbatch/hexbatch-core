<?php

namespace App\OpenApi\Users\CreateToken;

use App\Api\BaseParamsTrait;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use OpenApi\Attributes as OA;

/**
 * Any data in the body is set to the passthrough data for the token
 */

class CreateTokenParams
{
    use BaseParamsTrait;
    const int|float MAX_PASSTHROUGH_SIZE = 1024*20; //20k

    #[OA\Property( title: "Passthrough data (optional)", items: new OA\Items(), nullable: true)]
    /** @var mixed[] $response */
    protected array $passthrough;

    #[OA\Property(ref: HexbatchSecondsToLive::class, title: 'Seconds to live', nullable: true)]
    protected ?int $seconds = null;


    public function fromCollection(\Illuminate\Support\Collection $collection)
    {
        $this->passthrough = $collection->toArray();
        unset($this->passthrough['seconds_to_live']);
        if (mb_strlen(Utilities::maybeEncodeJson($this->passthrough) ) > static::MAX_PASSTHROUGH_SIZE) {
            throw new HexbatchNotPossibleException(__("msg.passthrough_data_too_big",['max'=>static::MAX_PASSTHROUGH_SIZE]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BAD_LOGIN);
        }
        $this->seconds = $this->intRefFromCollection($collection,'seconds_to_live');
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
