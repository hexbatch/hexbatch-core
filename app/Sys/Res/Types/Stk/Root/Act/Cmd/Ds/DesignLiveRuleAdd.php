<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Live\Params\LiveRuleParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Models\LiveRule;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;


/*
 * Add a single live rule to the design
 */

class DesignLiveRuleAdd extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = 'd6d8f371-fdc8-4465-89de-35e6c02456a5';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LIVE_RULE_ADD;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    #[ApiParamMarker( param_class: LiveRuleParamData::class)]
    public function __construct(
        protected LiveRuleParamData     $params,
        protected ElementType            $given_type,
        protected ?UserNamespace      $calling_namespace,
        protected bool          $do_permission_check = true,
    )
    {

    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'do_permission_check'=>$this->do_permission_check,
            'calling_namespace'=>$this->calling_namespace,
            'given_type'=>$this->given_type,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = LiveRuleParamData::from($args['params']);

        $do_permission_check = (bool)$args['do_permission_check'];
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args,false);
        $given_type = static::getTypeFromArray('given_type',$args);
        return new static(params: $params, given_type: $given_type, calling_namespace: $calling_namespace, do_permission_check: $do_permission_check);
    }




    public  function doCreateRule()
    : LiveRule
    {
        if ($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->calling_namespace,target: $this->given_type->owner_namespace);
        }

        $this->given_type->checkInUse();

        $trigger_type = ElementType::resolveType(value: $this->params->type_trigger);
        $target_type = ElementType::resolveType(value: $this->params->type_target);

        $given_rule = new LiveRule();
        $given_rule->live_rule_owner_type_id = $this->given_type->id;
        $given_rule->live_rule_trigger_type_id = $trigger_type->id;
        $given_rule->live_rule_target_type_id = $target_type->id;
        $given_rule->live_rule_policy = $this->params->live_rule_policy;
        $given_rule->is_passive = $this->params->is_passive;
        $given_rule->for_child_set_definers = $this->params->for_child_set_definers;
        $given_rule->live_rule_min_triggers = $this->params->live_rule_min_triggers;
        $given_rule->live_rule_max_triggers = $this->params->live_rule_max_triggers;
        $given_rule->type_owner_uuid = $this->given_type->owner_namespace->ref_uuid;
        $given_rule->type_target_uuid = $target_type->ref_uuid;
        $given_rule->type_trigger_uuid = $trigger_type->ref_uuid;
        $given_rule->save();
        return $given_rule;
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        $rule = null;
        if ($b_approved) {
            $rule = $work->doCreateRule();
        }

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['results'=>$rule]);

    }

    /**
     * @throws \Throwable
     */
    public static function makeTree(
        LiveRuleParamData     $params,
        UserNamespace          $calling_namespace,
        ElementType            $given_type,
        bool          $do_permission_check = true,
        ?IThangBuilder         $builder = null
    ) : Thang|IThangBuilder|null
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $me = new static(params: $params, given_type: $given_type, calling_namespace: $calling_namespace, do_permission_check: $do_permission_check);
        $builder?: $builder = ThangBuilder::createBuilder();


        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class],
            'command_args' => $me->toArray()
        ]);
        $builder->tree($my_command);

        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }

}

