<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Live\LiveRuleData;
use App\Data\ApiParams\Data\Live\Params\LiveRuleParamData;
use App\Models\ElementType;
use App\Models\LiveRule;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;

#[ApiParamMarker( param_class: LiveRuleParamData::class)]
class AddLiveRule extends Api\DesignApi implements ICommandCallable
{
    const UUID = 'bfda8c68-52ae-4e44-8616-0cfca871e338';
    const TYPE_NAME = 'api_design_add_live_rule';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLiveRuleAdd::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create rule");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['results'=>$children_args['results']]);
    }

    /**
     * @throws \Throwable
     */
    public static function createRule(LiveRuleParamData $params , UserNamespace  $calling_namespace, ElementType $given_type,
                                      bool          $do_permission_check = true,
                                       array $tags = [], ?IThangBuilder $builder = null)
    : LiveRuleData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['create-rule'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ds\DesignLiveRuleAdd::makeTree(
            params: $params,calling_namespace: $calling_namespace,given_type: $given_type,do_permission_check: $do_permission_check,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data['results'];
            $live =  LiveRule::getThisLiveRule(uuid: $data['ref_uuid'],b_do_relations: true);
            return LiveRuleData::makingUsingCodeArray($live);
        } else {
            return $thang;
        }

    }

}

