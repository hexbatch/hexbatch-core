<?php


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/*
 * rules should have old-fashioned events, signals, mutexes and semaphores
 *
 type signal base type
 rules can wait on signals, if a parent completes, the child rule will not wait more
  (if the parent logic decides enough children ran) the parent thing can short circuit
  example: two or children both waiting, if one captures signal, the other signal wait will be cancelled
 */

class Signal extends BaseType
{
    const UUID = '712aae22-0e42-4a3d-917f-b0ec9bd8fa78';
    const TYPE_NAME = 'signal';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Root::class
    ];

}
