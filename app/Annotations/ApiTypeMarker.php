<?php declare(strict_types=1);

namespace App\Annotations;



#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApiTypeMarker
{
    public function __construct(
        \App\Sys\Res\Types\Stk\Root\Api|string $api
    ) {

    }
}
