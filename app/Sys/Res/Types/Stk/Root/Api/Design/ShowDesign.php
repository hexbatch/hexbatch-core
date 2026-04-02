<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Data\ApiParams\Data\Types\ElementTypeData;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Api;



class ShowDesign extends Api\DesignApi
{
    const UUID = 'd3cbd497-e670-4cd9-9f80-88505d973747';
    const TYPE_NAME = 'api_design_show';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];

    public static function showDesign(ElementType $given_type) : ElementTypeData {
        $given_type->loadMissing('type_attributes','type_schedule','type_exposed_attributes','type_parents',
            'type_handle','owner_namespace', 'type_server', 'type_server_levels');
        return ElementTypeData::validateAndCreate($given_type);
    }

}

