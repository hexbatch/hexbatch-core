<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchFailException;
use App\Exceptions\RefCodes;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;

#[HexbatchTitle( title: "Remove one or more parents")]
#[HexbatchBlurb( blurb: "Parents can be removed from the design without any events raised")]
#[HexbatchDescription( description:'')]
class DesignParentRemove extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = 'bf333396-fdcc-45ac-977c-2a9be8f9840c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PARENT_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];




    public function __construct(
        protected ElementType   $given_type,
        protected ElementType   $parent_type,
        protected UserNamespace $caller_namespace,
        protected bool          $do_permission_check

    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'parent_type'=>$this->parent_type,
            'caller_namespace'=>$this->caller_namespace,
            'do_permission_check'=>$this->do_permission_check,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_type = static::getTypeFromArray('given_type',$args);
        $parent_type = static::getTypeFromArray('parent_type',$args) ;
        $caller_namespace =  static::getNamespaceFromArray('caller_namespace',$args) ;
        $do_permission_check = $args['do_permission_check'];
        return new static(given_type: $given_type,parent_type: $parent_type,
            caller_namespace: $caller_namespace,do_permission_check: $do_permission_check);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $updated_type = $work->doRemoveParentCall();
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $updated_type->toArray());
    }

    /**
     * @throws \Throwable
     */
    protected  function doRemoveParentCall() : ElementType {
        if ($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $this->given_type->owner_namespace);
        }

        $this->parent_type->loadMissing('type_parents');
        if ($this->do_permission_check) {
            foreach ($this->parent_type->type_parents as $par) {
                if ($par->ref_uuid === $this->parent_type->ref_uuid) {
                    $par->delete();
                    return $this->given_type;
                }
            }
        }

        throw new HexbatchFailException( __('msg.parent_type_is_invalid_cannot_remove',['ref'=>$this->given_type->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
            RefCodes::TYPE_PARENT_CANNOT_BE_REMOVED);

    }



    /**
     * @throws \Throwable
     */
    public static function removeParent(
        UserNamespace $calling_namespace,ElementType $given_type,ElementType $parent_type, bool $do_permission_check,
        ?IThangBuilder $builder = null
    ) : ElementType|Thang|IThangBuilder
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);


        $builder->tree(
            command_class: Act\Cmd\Ds\DesignParentRemove::class,
            command_args: (array)new Act\Cmd\Ds\DesignParentRemove(
                given_type:$given_type,
                parent_type: $parent_type,
                caller_namespace: $calling_namespace,
                do_permission_check: $do_permission_check
            ),
            command_tags: [Act\Cmd\Ds\DesignParentRemove::class]
        );



        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  ElementType::getElementType(uuid: $data['ref_uuid']);
        } else {
            return $thang;
        }

    }


}

