<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Sys\Res\Atr\INewSystemAttribute;
use App\Sys\Res\Atr\Stk\Placeholder\MarkerData;
use App\Sys\Res\Types\INewSystemType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Marker\TransferNamespaceMarker;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\DB;


class NamespaceTransferDo extends Act\Cmd\Ns implements ICommandCallable
{
    use Act\Cmd\Ns\Traits\NamespaceDoAction;

    const UUID = 'fe81b6d9-88ae-44d3-aa7e-790b72e3c68c';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_DO_TRANSFER;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceTransfered::class  //after the fact
    ];

    const string|INewSystemType MARKER_CLASS = TransferNamespaceMarker::class;
    const string|INewSystemAttribute ATTRIBUTE_CLASS = MarkerData::class;

    const string|Evt\Server\Traits\IServerEvent|null POST_EVENT_CLASS = Evt\Server\NamespaceTransfered::class;

    protected function getInnerUuid(): ?string
    {
        return $this->target_namespace->ref_uuid;
    }

    protected function getInnerType(): ?ElementType
    {
        return $this->target_namespace->namespace_base_type;
    }

    protected function doInnerAction()
    {
        DB::transaction(function (){
            $this->target_namespace->namespace_user_id = $this->target_user->id;
            $this->target_namespace->save();
        });
    }
}

