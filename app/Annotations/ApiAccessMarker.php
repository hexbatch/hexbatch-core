<?php declare(strict_types=1);

namespace App\Annotations;



use App\Annotations\Access\TypeOfAccessMarker;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApiAccessMarker
{

    /** @noinspection PhpUnused */
    public function __construct(
        TypeOfAccessMarker $marker
    ) {

    }
}
