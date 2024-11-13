<?php declare(strict_types=1);

namespace App\Helpers\Annotations;



#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApiEventMarker
{
    public function __construct(
        \App\Sys\Res\Types\Stk\Root\Event|string $api
    ) {

    }
}
