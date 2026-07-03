<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


#[ApiParamMarker( param_class: SelectElementParamData::class)]
class SwitchOn extends SwitchOff
{
    const UUID = '1570126c-e9b8-4fca-a525-078a74ce5ab1';
    const TYPE_NAME = 'api_element_type_on';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\SwitchOn::class,
    ];

    const ACTION_CLASS = Act\Cmd\Ele\SwitchOn::class;

}

