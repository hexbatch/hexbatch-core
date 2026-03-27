<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiEventMarker;
use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Attributes\Params\AttributeParamData;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;
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
use App\Sys\Res\Types\Stk\Root\Evt;

#[ApiParamMarker( param_class: AttributeParamData::class)]
#[ApiEventMarker( Evt\Server\AttributePending::class)]
class CreateAttribute extends Api\DesignApi implements ICommandCallable
{
    const UUID = '745c1851-68af-4420-b6f9-037aa63bebc7';
    const TYPE_NAME = 'api_design_create_attribute';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeCreate::class,
    ];


    public function __construct(
        protected AttributeParamData $params,
        protected ElementType $given_type,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create attribute node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function createAttribute(UserNamespace      $namespace, ElementType $given_type,
                                         AttributeParamData $params , array $tags = [], ?IThangBuilder $builder = null)
    : Attribute|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['create-attribute'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->tree($my_command)
            ->leaf(
                command_class: Evt\Server\DesignPending::class,
                command_args: [
                    'attribute_params'=>$params->toArray(),
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid,
                    'namespace'=>Utilities::getCurrentNamespace(),
                    'given_type'=>$given_type,
                ],
                command_tags: [Evt\Server\DesignPending::class]
            )
            ->tree(
                command_class: Act\Cmd\Ds\DesignAttributeCreate::class,
                command_args: [
                    'attribute_params'=>$params->toArray(),
                    'namespace'=>Utilities::getCurrentNamespace(),
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid,
                    'given_type'=>$given_type,
                ],
                command_tags: [Act\Cmd\Ds\DesignAttributeCreate::class]
            );

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  Attribute::getThisAttribute(uuid: $data['ref_uuid'],b_do_relations: true);
        } else {
            return $thang;
        }

    }

}

