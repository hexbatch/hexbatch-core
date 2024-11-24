<?php

namespace App\Api\Calls\User\CreateToken;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_seconds_to_live',
    title: 'Seconds to live',
    description: 'When needing to give a length in seconds up to a year',
    type: 'integer',
    format: 'int32',
    maxLength: HexbatchSecondsToLive::MAX_SECONDS,
    minLength: 1,
    example: [new OA\Examples(summary: "A lifetime of 6 months", value:'15768000') ]
)]
class HexbatchSecondsToLive
{
    const MAX_SECONDS = 60*60*24*365;

}
