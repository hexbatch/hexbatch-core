<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Types\Params\TypeSearchParams;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\ElementType;

use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Api\ListTypeTrait;
use Spatie\LaravelData\CursorPaginatedDataCollection;

#[ApiParamMarker( param_class: TypeSearchParams::class)]
class ListDesigns extends Api\DesignApi
{
    use ListTypeTrait;
    const UUID = '8b1513d3-5a01-4e6f-979e-3584bbec14af';
    const TYPE_NAME = 'api_design_list';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];

    /**
     * @return CursorPaginatedDataCollection<ElementType>
     */
    public static function listDesigns(UserNamespace $calling_namespace,?TypeSearchParams $params)
    {
        return static::listCursoratedTypes(calling_namespace: $calling_namespace,params: $params, lifecycle: TypeOfLifecycle::DEVELOPING);
    }

}

