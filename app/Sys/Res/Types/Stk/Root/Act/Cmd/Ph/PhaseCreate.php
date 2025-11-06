<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Phases\Params\PhaseParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\DB;


#[ApiParamMarker( param_class: PhaseParamData::class)]
class PhaseCreate extends Act\Cmd\Ph implements ICommandCallable
{
    const UUID = '24d33a5b-ed63-48f4-b45d-f729734af6ef';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_CREATE;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ph::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\PhaseAdded::class,
    ];



    public function __construct(
        protected ?PhaseParamData $params,
        protected ?ElementType    $origin_type,
        protected bool            $is_system,
        protected ?UserNamespace  $calling_namespace,
        protected ?string         $use_ref = null,
        protected ?Phase $editing_phase = null,

    )
    {
        if (!$this->editing_phase && $this->params->editing_phase_ref) {
            $this->editing_phase = Phase::getThisPhase(uuid: $this->params->editing_phase_ref);
        }
    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'use_ref'=>$this->use_ref,
            'given_set'=>$this->origin_type,
            'is_system'=>$this->is_system,
            'calling_namespace'=>$this->calling_namespace,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = $args['params'] ? PhaseParamData::from($args['params']) : null ;
        $is_system = (bool)$args['is_system'];
        $use_ref = $args['use_ref'];
        $given_set = static::getSetFromArray('given_set',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args,false );
        return new static(params: $params, origin_type: $given_set,is_system: $is_system,calling_namespace: $calling_namespace,use_ref: $use_ref);
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $phase = $work->makePhase();
            if (!$work->is_system)
            {
                $r = new Evt\Server\PhaseAdded(given_type: $work->origin_type,given_phase: $phase);
                $r->callTreeByItself($children_args);
            }
        } else {
            $phase = null ;
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'phase'=>$phase]);
    }



    /**
     * @throws
     */
    public function makePhase( bool $is_default_phase = false,bool $b_do_refresh = true ) : Phase{

        try {
            DB::beginTransaction();

            $phase = new Phase();
            if ($this->use_ref) {
                $phase->ref_uuid = $this->use_ref;
            }

            $phase->phase_type_id = $this->origin_type->id;
            $phase->setPhaseName($this->params->name);
            $phase->is_default_phase = $is_default_phase;
            $phase->edited_by_phase_id = $this->editing_phase?->id;
            $phase->is_system = $this->is_system;
            $phase->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($b_do_refresh) {
            $phase->refresh();
        }

        return $phase;
    }



    /**
     * @throws \Throwable
     */
    public static function createPhase(
        PhaseParamData     $params,
        ElementType                $originating_type,
        bool                      $is_system,
        UserNamespace             $calling_namespace,
        ?IThangBuilder              $builder = null
    ) : Thang|IThangBuilder
    {

        if (!$is_system) {
            static::checkIfGivenIsAdmin(given: $calling_namespace,target: $originating_type->owner_namespace);
        }

        $me = new static(
            params: $params,
            origin_type: $originating_type,
            is_system: $is_system,
            calling_namespace: $calling_namespace
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);


        $builder->tree(
            command_class: static::class,
            command_args: $me->toArray(),
            command_tags: [static::class],
        );


        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        return $thang;
    }


}

