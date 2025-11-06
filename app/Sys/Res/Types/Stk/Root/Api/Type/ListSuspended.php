<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Data\ApiParams\Data\Types\ElementTypeData;
use App\Data\ApiParams\Data\Types\Params\TypeSearchParams;
use App\Enums\Types\TypeOfLifecycle;

use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;
use Spatie\LaravelData\CursorPaginatedDataCollection;


class ListSuspended extends Api\TypeApi
{
    use Api\ListTypeTrait;
    const UUID = 'd7ca746f-c541-4f6d-b7a8-165434499922';
    const TYPE_NAME = 'api_type_list_suspended';


    const PARENT_CLASSES = [
        Api\TypeApi::class
    ];


    /**
     * @return CursorPaginatedDataCollection<ElementTypeData>
     */
    public static function listSuspended(UserNamespace $calling_namespace,?TypeSearchParams $params)
    {
        return static::listCursoratedTypes(calling_namespace: $calling_namespace,params: $params, lifecycle: TypeOfLifecycle::SUSPENDED);
    }



}

