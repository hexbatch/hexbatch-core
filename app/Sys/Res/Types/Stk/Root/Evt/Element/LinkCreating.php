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


class LinkCreating extends Evt\ScopeElement implements ICommandCallable
{
    use ElementBlockingEventTree;

    const UUID = '22a1dfad-8550-468f-9288-84075af7cf2b';
    const EVENT_NAME = TypeOfEvent::LINK_CREATING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

    public function __construct(
        protected Element             $given_element,
        protected ?ElementSet             $given_set
    )
    {

    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'turning on');
    }

    protected function decide() : bool {
        return true;
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

