<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Models\ElementType;
use App\Sys\Res\Atr\INewSystemAttribute;
use App\Sys\Res\Atr\Stk\Placeholder\MarkerData;
use App\Sys\Res\Types\INewSystemType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Marker\DeletingUserMarker;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\DB;

/**
 * applies live on the user's private token:  @uses \App\Sys\Res\Types\Stk\Root\Namespace\DeletingUserMarker
 */
class UserDeleteDo extends Act\Cmd\Us implements ICommandCallable
{
    use Act\Cmd\Ns\Traits\NamespaceDoAction;

    const UUID = '0a221da7-3e9b-46b0-b181-a67a27aa4065';
    const ACTION_NAME = TypeOfAction::CMD_USER_DELETE;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Us::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\UserDeleted::class
    ];

    const string|INewSystemType MARKER_CLASS = DeletingUserMarker::class;
    const string|INewSystemAttribute ATTRIBUTE_CLASS = MarkerData::class;

    const string|Evt\Server\Traits\IServerEvent|null POST_EVENT_CLASS = Evt\Server\UserDeleted::class;

    protected function getInnerUuid(): ?string
    {
        return $this->target_user->ref_uuid;
    }

    protected function getInnerType(): ?ElementType
    {
        return null;
    }

    protected function doInnerAction()
    {
        DB::transaction(function (){
            $this->target_namespace->delete();
        });
    }
}

