<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Data\ApiParams\Data\Attributes\AttributeData;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Sys\Res\Types\Stk\Root\Api;



class ShowAttribute extends Api\DesignApi
{
    const UUID = '681e3f6e-9410-4356-a157-4d99580c0232';
    const TYPE_NAME = 'api_design_show_attribute';

    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];


    public function __construct(
        protected ?Attribute $att = null,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }



    public static function showAttribute(Attribute $att) : AttributeData {
        $att->loadMissing('attribute_parent','type_owner','attribute_location','attribute_design');
        return AttributeData::validateAndCreate($att);
    }

}

