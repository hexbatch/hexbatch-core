<?php

namespace App\Enums\Sys;



use App\Data\ApiParams\Enums\EnumTryTrait;

enum TypeOfFlag: string
{
    use EnumTryTrait;
    case CAN_WRITE = 'can_write';
    case CAN_READ = 'can_read';

}
