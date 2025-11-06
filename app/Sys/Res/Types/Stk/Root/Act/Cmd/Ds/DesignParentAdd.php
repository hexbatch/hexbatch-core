<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Models\ElementTypeParent;
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

#[HexbatchTitle( title: "Add a parent to the type")]
#[HexbatchBlurb( blurb: "Parents can be given to types in design mode. Some parents have to agree")]
#[HexbatchDescription( description:'')]
class DesignParentAdd extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = '362a3cdf-f013-4bc0-afce-315cba179544';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PARENT_ADD;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\DesignParentAdding::class
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
        $updated_type = $work->doAddParentCall();
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $updated_type->toArray());
    }

    /**
     * @throws \Throwable
     */
    protected  function doAddParentCall() : ElementType {
        if ($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $this->given_type->owner_namespace);
        }


        if ($this->do_permission_check) {
            $this->parent_type->loadMissing('type_parents');
            //already added
            if (array_any($this->parent_type->type_parents, fn($par) => $par->ref_uuid === $this->parent_type->ref_uuid)) {
                return $this->given_type;
            }
        }

        ElementTypeParent::addOrUpdateParent(parent: $this->parent_type, child: $this->given_type, check_parent_published: !!$this->do_permission_check);

        return $this->given_type;
    }


    /**
     * @throws \Throwable
     */
    public static function addParent(
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


        if ($do_permission_check)
        {
            $tree = Evt\Type\DesignParentAdding::callParentTree(given_type: $given_type,parent_type: $parent_type,builder: $builder);

            $tree->leaf(
                command_class: Act\Cmd\Ds\DesignParentAdd::class,
                command_args: new Act\Cmd\Ds\DesignParentAdd(
                    given_type:$given_type,
                    parent_type: $parent_type,
                    caller_namespace: $calling_namespace,
                    do_permission_check: true
                )->toArray(),
                command_tags: [Act\Cmd\Ds\DesignParentAdd::class],
                command_priority: -1
            );
        } else {
            $builder->tree(
                command_class: Act\Cmd\Ds\DesignParentAdd::class,
                command_args: new Act\Cmd\Ds\DesignParentAdd(
                    given_type:$given_type,
                    parent_type: $parent_type,
                    caller_namespace: $calling_namespace,
                    do_permission_check: false
                )->toArray(),
                command_tags: [Act\Cmd\Ds\DesignParentAdd::class]
            );
        }



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

