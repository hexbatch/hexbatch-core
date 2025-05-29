<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class EditTime extends Api\DesignApi
{
    const UUID = '0a0c55b3-a608-42b8-b9cc-373601e74757';
    const TYPE_NAME = 'api_design_edit_time';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeEdit::class,
    ];

}

