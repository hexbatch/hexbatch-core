<?php declare(strict_types=1);

namespace App\Helpers\Annotations;



use App\Helpers\Annotations\Access\TypeOfAccessMarker;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApiAccessMarker
{

    public function __construct(
        TypeOfAccessMarker $marker
    ) {

    }
}
