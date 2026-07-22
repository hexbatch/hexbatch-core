<?php
namespace App\Enums\Paths;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_path_status
 */

enum TypeOfPathStatus : string {
    use EnumTryTrait;
  case DESIGN = 'design';
  case ready = 'ready';
  case error = 'error';
  case sabotaged = 'sabotaged';



}


