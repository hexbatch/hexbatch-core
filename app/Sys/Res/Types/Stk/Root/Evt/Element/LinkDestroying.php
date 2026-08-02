<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\ElementBlockingEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;


class LinkDestroying extends Evt\ScopeElement implements ICommandCallable
{
    use ElementBlockingEventTree;

    const UUID = 'fcdeae99-6b45-4183-8c7a-a3511e18ec3b';
    const EVENT_NAME = TypeOfEvent::LINK_DESTROYING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'turning on');
    }



    /**
     * @throws \Throwable
     */
    public static function callEventTree(
        Element               $given_element,
        ?ElementSet             $given_set,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null
    {
        return static::callEventTreeInner(given_element: $given_element,given_set: $given_set,builder: $builder,b_ask_set: true );
    }

}

