<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Data\ApiParams\Data\Types\Params\TypeSearchParams;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use Spatie\LaravelData\CursorPaginatedDataCollection;


class ListPublished extends Api\TypeApi
{
    use Api\ListTypeTrait;

    const UUID = '9439a2ea-427a-468f-9bdf-9a5fb58157b6';
    const TYPE_NAME = 'api_type_list_published';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Pa\Search::class
    ];

    /**
     * @return CursorPaginatedDataCollection<ElementType>
     */
    public static function listPublished(UserNamespace $calling_namespace,?TypeSearchParams $params)
    {
        return static::listCursoratedTypes(calling_namespace: $calling_namespace,params: $params, lifecycle: TypeOfLifecycle::PUBLISHED);
    }

}

