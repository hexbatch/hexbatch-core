<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Events\IEventReference;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;


class AttributeWrite extends Evt\ScopeSet implements ICommandCallable
{
    const UUID = 'a1b06d04-7ac4-43a1-8353-a3f9c7df1b94';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_WRITE;




    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function __construct(
        protected ?string         $given_set_ref,

        /** @var Collection<IEventReference>|null $write_events */
        protected Collection|null $write_events,
    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_set_ref'=>$this->given_set_ref,
            'write_events'=>$this->write_events->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $write_events =static::getEventCollectionFromArray('write_events',$args,false);
        $given_set_ref = $args['given_set_ref']??null;

        return new static(given_set_ref: $given_set_ref, write_events: $write_events);
    }

    /**
     * @param array $children_args
     * @param array $command_args organized by element_ref_values key having hash element_ref => [attribute_ref,data]
     * @return ICmdCallReturn
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data:
            [
                'children'=> $children_args,
                'given_set_ref' => $work->given_set_ref,
                'attribute_ref_values' => $children_args['attribute_ref_values']??[],
            ]
        );
    }


    /**
     * @throws \Throwable
     */
    public static function callEventTree(
        ?string        $given_set_ref,

        /** @var Collection<IEventReference> $read_events */
        Collection     $write_events,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null
    {
        /*
        * notifications can be either for the type of the element or the type of the definer in the set
        */


        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();

        $me = new static(given_set_ref: $given_set_ref, write_events: $write_events);
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class,'write-events'],
            'command_args' => $me->toArray()
        ]);

        $builder->tree($my_command);


        /** @var IEventReference $ref */
        foreach ($write_events as $ref) {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: new Evt\EventHandler(
                    ref: $ref,
                    collection_context: $ref->getReferences(),
                    set_ref: $given_set_ref,
                )->toArray(),
                command_tags: [Evt\EventHandler::class]
            );
        }


        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();
    }



}

