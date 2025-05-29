<?php declare(strict_types=1);

namespace App\Annotations;



use App\OpenApi\IApiParam;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApiParamMarker
{
    /** @noinspection PhpUnused */
    public function __construct(
        IApiParam|string $param_class
    ) {

    }
}
