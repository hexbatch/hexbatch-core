<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Data\ApiParams\Data\Types\ElementTypeData;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Api;
use Carbon\Carbon;


class ShowType extends Api\TypeApi
{
    const UUID = '43468b50-f2c9-468b-ae37-9dcf02332ea7';
    const TYPE_NAME = 'api_type_show';


    const PARENT_CLASSES = [
        Api\TypeApi::class
    ];

    public static function showType(ElementType $given_type) : ElementTypeData {
        $given_type->loadMissing(
            'type_attributes',
            'type_schedule',
            'type_schedule.time_spans',
            'type_exposed_attributes',
//            'type_parents',
            'type_handle',
            'owner_namespace',
            'type_server',
            'type_server_levels'
        );
//        $what = $given_type->toArray();
//        $what['created_at'] = Carbon::createFromTimeString($what['created_at'])->toAtomString();
//        $what['updated_at'] = Carbon::createFromTimeString($what['updated_at'])->toAtomString();
        return ElementTypeData::MakingUsingCodeArray($given_type);
    }

}

