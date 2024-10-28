<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class EditPart extends BaseType
{
    const UUID = 'f42dedd5-2883-4864-978a-533c9040dc1c';
    const TYPE_NAME = 'api_path_edit_part';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Pa\PathPartEdit::class,
    ];

}

