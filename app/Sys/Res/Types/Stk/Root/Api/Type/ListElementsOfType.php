<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;

use App\Sys\Res\Types\Stk\Root\Api;


#[ApiParamMarker( param_class: SelectElementParamData::class)]
class ListElementsOfType extends Api\Element\ListElements
{
    const UUID = '70e9fd26-ab9c-4259-a4d4-32ff4803868f';
    const TYPE_NAME = 'api_type_list_elements';


    const PARENT_CLASSES = [
        Api\TypeApi::class
    ];


}

