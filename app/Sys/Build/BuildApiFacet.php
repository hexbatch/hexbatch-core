<?php
namespace App\Sys\Build;
/**
 * postgres enum type_of_approval
 */
enum BuildApiFacet : string {

    case FACET_SETUP = 'FACET_SETUP';
    case FACET_RESPONSE = 'FACET_RESPONSE';
    case FACET_PARAMS = 'FACET_PARAMS';
    case FACET_RESULT = 'FACET_RESULT';

}


