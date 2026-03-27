<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Models\ActionDatum;
use App\Models\Attribute;
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


class DestroyAttribute extends Api\DesignApi implements ICommandCallable
{
    const UUID = '9ab860e3-fff0-4fdd-b18c-f9b33365692f';
    const TYPE_NAME = 'api_design_destroy_attribute';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeDestroy::class,
    ];


    public function __construct(
        protected Attribute $given_attribute,

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
        Log::debug("Called api delete attribute node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function destoryAttribute(UserNamespace      $namespace, Attribute $given_attribute,
                                            array $tags = [], ?IThangBuilder $builder = null)
    : Attribute|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['destroy-attribute'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->tree($my_command)
            ->leaf(
                command_class: Act\Cmd\Ds\DesignAttributeDestroy::class,
                command_args: [
                    'namespace'=>$namespace,
                    'namespace_uuid'=>$namespace->ref_uuid,
                    'given_attribute'=>$given_attribute,
                ],
                command_tags: [Act\Cmd\Ds\DesignAttributeDestroy::class]
            )
            ;

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  Attribute::getThisAttribute(uuid: $data['ref_uuid'],b_do_relations: true);
        } else {
            return $thang;
        }

    }
}

