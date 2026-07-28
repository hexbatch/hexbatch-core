<?php
namespace App\Enums\Attributes;
use App\Data\ApiParams\Enums\EnumTryTrait;
use OpenApi\Attributes as OA;

/**
 * postgres enum type_of_element_value_policy
 */
#[OA\Schema(schema: 'TypeOfElementValuePolicy',title: "Element value policy")]
enum TypeOfElementValuePolicy : string {

    use EnumTryTrait;

    case STATIC = 'static';

    case PER_ELEMENT = 'per_element';

    case PER_SET = 'per_set';

    case PER_CHILD = 'per_set_chain';


}


