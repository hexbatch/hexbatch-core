<?php
namespace App\Sys\Build;
/**
 * postgres enum type_of_approval
 */
enum BuildActionFacet : string {

    case FACET_INPUT = 'FACET_INPUT';
    case FACET_OUTPUT = 'FACET_OUTPUT';
    case FACET_PARAMS = 'FACET_PARAMS';
    case FACET_WORKER = 'FACET_WORKER';
    case FACET_RETURN = 'FACET_RETURN';

}


