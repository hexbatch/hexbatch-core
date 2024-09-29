<?php
namespace App\Enums\Things;
/**
 * postgres enum type_filter_set_usage
 */
enum TypeFilterSetUsage : string {

  case NONE = 'none';
  case MUST_MATCH = 'must_match';
  case MUST_EXCLUDE = 'must_exclude';

}


