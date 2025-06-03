<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypePublish;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Signal\Semaphore\MasterSemaphore;

/**
 * @see MasterSemaphore for the setup,
 * it takes a type made for this, which is already published,
 * and another optional published semaphore type to use in chained masters
 *
 * This makes the new types and rules for the master group,
 * all the types are in a publishing group with a handle of an element of the type given
 * The types are returned in developer mode so stuff can be added
 * Master can be used after publishing @uses TypePublish
 *
 */
class SemaphoreMasterCreate extends Act\Cmd\Wa
{
    const UUID = 'e6bf1d5c-0bf3-440c-8e29-9f18cee4d409';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_MASTER_CREATE;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\ElementCreation::class,
        Evt\Server\SetCreated::class,
        Evt\Server\TypeOwnerChange::class,
        Evt\Server\DesignPending::class,
        Evt\Server\TypePublished::class,
    ];

}

