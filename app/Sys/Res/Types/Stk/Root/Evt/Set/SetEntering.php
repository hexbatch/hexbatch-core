<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;


class SetEntering extends Evt\ScopeSet implements ICommandCallable
{
    use Evt\Set\Traits\SetBlockingEventTree;

    const UUID = 'b661ae85-8abd-4228-adf9-f6518788c7d1';
    const EVENT_NAME = TypeOfEvent::SET_ENTERING;


    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function __construct(
        protected ElementSet            $given_set,
        protected Element             $given_element

    )
    {

    }


    protected  function toArray() :array {
        return [
            'given_element'=>$this->given_element,
            'given_set'=>$this->given_set,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_element = static::getElementFromArray('given_element',$args);
        $given_set = static::getSetFromArray('given_set',$args);

        return new static(given_set: $given_set, given_element: $given_element);
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'set entering');
    }

    protected function decide() : bool {
        return true;
    }

    /**
     * @throws \Throwable
     */
    public static function callEventTree(
         ElementSet            $given_set,
         Element               $given_element,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null
    {
        return static::callEventTreeInner($given_set,$given_element,$builder);
    }



}

