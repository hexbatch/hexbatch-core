<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;

use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Api\Element\ListElements;


#[ApiParamMarker( param_class: SelectElementParamData::class)]
class ListMembers extends ListElements
{
    const UUID = 'cd570e6a-8a1f-4d96-9cfa-76708d501346';
    const TYPE_NAME = 'api_set_list_members';

    const PARENT_CLASSES = [
        Api\SetApi::class
    ];


}

