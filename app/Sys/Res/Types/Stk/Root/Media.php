<?php


namespace App\Sys\Res\Types\Stk\Root;

use App\Models\ActionDatum;
use App\Sys\Res\Atr\Stk\Media\DownloadableUrl;
use App\Sys\Res\Atr\Stk\Media\MediaType;
use App\Sys\Res\Atr\Stk\Media\MediaUrl;
use App\Sys\Res\Atr\Stk\MetaData\Media\MediaData;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;


class Media extends BaseType
{
    const UUID = 'e11798f3-f23c-46b1-95a4-c868bb5e0f16';
    const TYPE_NAME = 'media';



    const ATTRIBUTE_CLASSES = [
        MediaData::class,
        MediaUrl::class,
        DownloadableUrl::class,
        MediaType::class,
    ];

    const PARENT_CLASSES = [
        Root::class,
    ];

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->is_public_domain = true;
        return $this->action_data;
    }

}
