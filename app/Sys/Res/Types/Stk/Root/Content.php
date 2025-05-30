<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stk\Root;

use App\Models\ActionDatum;
use App\Sys\Res\Atr\Stk\MetaData\Content\ContentData;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Content extends BaseType
{
    const UUID = 'c9f9671e-905f-4198-9049-ef0e1ad4d268';
    const TYPE_NAME = 'content';



    const ATTRIBUTE_CLASSES = [
        ContentData::class,
        ContentData\Subject::class,
        ContentData\Tags::class,
        ContentData\Blurb::class,
        ContentData\Body::class,
    ];

    const PARENT_CLASSES = [
        Root::class
    ];

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->is_public_domain = true;
        return $this->action_data;
    }

}

