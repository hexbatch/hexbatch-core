<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Models\Element;
use App\Models\ElementType;
use App\Sys\Res\Atr\INewSystemAttribute;
use App\Sys\Res\Atr\Stk\Placeholder\MarkerData;
use App\Sys\Res\Types\INewSystemType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Marker\DeletingNamespaceMarker;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\DB;

class NamespaceDestroyDo extends Act\Cmd\Ns implements ICommandCallable
{
    use Act\Cmd\Ns\Traits\NamespaceDoAction;

    const UUID = '0253a9c0-78db-4f8d-b648-7d2abd5ac47c';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_DESTROY;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceDestroyed::class
    ];

    const string|INewSystemType MARKER_CLASS = DeletingNamespaceMarker::class;
    const string|INewSystemAttribute ATTRIBUTE_CLASS = MarkerData::class;

    const string|Evt\Server\Traits\IServerEvent|null POST_EVENT_CLASS = Evt\Element\NamespaceStartingTransfer::class;


    protected function getInnerUuid() : ?string {
        return $this->target_namespace->ref_uuid;
    }

    protected function getInnerType() : ?ElementType {
        return $this->target_namespace->namespace_base_type;
    }

    /**
     * @throws \Throwable
     */
    protected function doInnerAction() {
        DB::transaction(function (){
            if ($this->params->transfer_elements_to_default) {
                Element::where('element_namespace_id',$this->target_namespace->id)
                    ->update(['element_namespace_id' => $this->target_namespace->owner_user->default_namespace->id]);
            }

            if ($this->params->transfer_types_to_default) {
                ElementType::where('owner_namespace_id',$this->target_namespace->id)
                    ->update(['owner_namespace_id' => $this->target_namespace->owner_user->default_namespace->id]);
            }
            $this->target_namespace->delete();
        });
    }

}

