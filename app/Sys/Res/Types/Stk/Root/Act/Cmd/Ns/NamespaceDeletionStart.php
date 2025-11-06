<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\ChangeNamespacesParamData;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\INewSystemType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\IElementEvent;
use App\Sys\Res\Types\Stk\Root\Marker\DeletingNamespaceMarker;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


#[ApiParamMarker( param_class: ChangeNamespacesParamData::class)]
class NamespaceDeletionStart extends Act\Cmd\Ns implements ICommandCallable
{
    use Act\Cmd\Ns\Traits\NamespaceStartAction;

    const UUID = 'efb8d969-20a6-4bd7-9f43-4e0448338931';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_PREP_DELETION;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [];

    const string|INewSystemType MARKER_CLASS = DeletingNamespaceMarker::class;
    const string|IElementEvent|null PRE_EVENT_CLASS = null;





}

