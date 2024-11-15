<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypePublishMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Server\TypePublished;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Publishes the type, any referenced parent types, parent attributes, live rules, live requirements
 * are given the event of @see TypePublished and all must agree
 *
 */
class TypePublish extends Act\Cmd\Ty
{
    const UUID = 'af28da1b-b148-4cbf-a53f-ccaf641373ea';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_PUBLISH;


    const ATTRIBUTE_CLASSES = [
        TypePublishMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypePublished::class
    ];

}
