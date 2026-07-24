<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Data\ApiParams\Data\Attributes\AttributeData;
use App\Models\Attribute;
use App\Sys\Res\Types\Stk\Root\Api;



class ShowAttribute extends Api\DesignApi
{
    const UUID = '681e3f6e-9410-4356-a157-4d99580c0232';
    const TYPE_NAME = 'api_design_show_attribute';

    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];





    public static function showAttribute(Attribute $att) : AttributeData {
        $att->loadMissing(
            'attribute_parent',
            'type_owner',
            'attribute_location',
             'attribute_design',
            'attribute_ancestors'
        );
        $att->type = $att->type_owner;
        return AttributeData::MakingUsingCodeArray($att);
    }

}

