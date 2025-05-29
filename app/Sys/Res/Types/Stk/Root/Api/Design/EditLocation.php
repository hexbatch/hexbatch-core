<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class EditLocation extends Api\DesignApi
{
    const UUID = '092ffcc2-8feb-4922-9325-fcb3d197886d';
    const TYPE_NAME = 'api_design_edit_location';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationEdit::class,
    ];

}

