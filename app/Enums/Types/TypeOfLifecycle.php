<?php
namespace App\Enums\Types;
use App\Data\ApiParams\Enums\EnumTryTrait;
use Illuminate\Support\Collection;

/**
 * postgres enum type_of_lifecycle
 */

enum TypeOfLifecycle : string {
    use EnumTryTrait;
  case DEVELOPING = 'developing';
  case PUBLISHED = 'published';
  case RETIRED = 'retired';
  case SUSPENDED = 'suspended';
  //note suspended only done by server admin, suspended types cannot make new elements , their existing elements are  force destroyed, elsewhere is told about suspension



}


