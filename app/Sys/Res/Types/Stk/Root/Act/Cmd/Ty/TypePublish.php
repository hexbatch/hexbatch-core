<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchFailException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\AttributeAncestor;
use App\Models\ElementType;
use App\Models\ElementTypeAncestor;
use App\Models\ElementTypeExposedAttribute;
use App\Models\ElementValue;
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

/**
 * Publishes the type, any referenced parent types, parent attributes, live rules, live requirements
 * are given the event of @see TypePublishing and all must agree
 */
class TypePublish extends Act\Cmd\Ty implements ICommandCallable
{
    const UUID = 'af28da1b-b148-4cbf-a53f-ccaf641373ea';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_PUBLISH;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypePublishing::class
    ];



    public function __construct(
        protected ElementType   $given_type,
        protected UserNamespace $caller_namespace,
        protected bool          $do_permission_check

    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'caller_namespace'=>$this->caller_namespace,
            'do_permission_check'=>$this->do_permission_check,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_type = static::getTypeFromArray('given_type',$args);
        $caller_namespace =  static::getNamespaceFromArray('caller_namespace',$args) ;
        $do_permission_check = $args['do_permission_check'];
        return new static(given_type: $given_type, caller_namespace: $caller_namespace,do_permission_check: $do_permission_check);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = $children_args[static::CHILD_DECISION_KEY]??false;
        if ($b_approved) {
            $updated_type = $work->doPublishCall();
        } else {
            $updated_type = $work->given_type;
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'type'=>$updated_type]);
    }

    /**
     * @throws \Throwable
     */
    protected  function doPublishCall() : ElementType {

        $this->checkForAbstractAttributes();

        if ($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $this->given_type->owner_namespace);
            $this->checkAttrForApproval();
            $this->checkParentsForApproval();
        }

        DB::transaction(function() {
            $this->given_type->lifecycle = TypeOfLifecycle::PUBLISHED;
            $this->given_type->save();

            foreach ($this->given_type->type_attributes as $att) {
                ElementValue::maybeAssignStaticValue(att: $att);
            }

            ElementTypeExposedAttribute::makeRecords(type: $this->given_type);
            AttributeAncestor::makeRecordsForType(type: $this->given_type);
            ElementTypeAncestor::makeRecordsForType(type: $this->given_type);
        });



        return $this->given_type;
    }

    protected  function checkAttrForApproval() {


        $names = [];
        foreach ($this->given_type->type_attributes as $att) {
            if ($att->attribute_approval !== TypeOfApproval::DESIGN_APPROVED) {
                $names[] = $att->getName();
            }
        }

        if (count($names)) {
            throw new HexbatchNotPossibleException(__('msg.attribute_parents_did_not_approve_design',
                ['ref' => $this->given_type->getName(), 'child' => implode('|', $names)]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_PARENT_DENIED_DESIGN);
        }
    }

    protected  function checkForAbstractAttributes() {

        $names = [];
        foreach ($this->given_type->type_attributes as $att) {
            if ($att->is_abstract) {
                $names[] = $att->getName();
            }
        }
        if (count($names)) {
            throw new HexbatchNotPossibleException(__('msg.type_has_abstract_attribute',
                ['ref'=>$this->given_type->getName(),'issues'=>implode('|',$names)]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_CANNOT_PUBLISH_ABSTRACT);
        }

    }

    protected  function checkParentsForApproval() {
        $names = [];
        foreach ($this->given_type->type_parents as $par) {
            if ($par->parent_type_approval !== TypeOfApproval::DESIGN_APPROVED) {
                $names[] = $par->getName();
            }
        }
        if (count($names)) {
            throw new HexbatchFailException(__('msg.design_parents_did_not_approve_design', [
                'ref' => implode('|', $names),
                'child' => $this->given_type->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_PARENT_DENIED_DESIGN);
        }
    }


    /**
     * @throws \Throwable
     */
    public static function publish(
        UserNamespace $calling_namespace,ElementType $given_type, bool $do_permission_check,
        ?IThangBuilder $builder = null
    ) : ElementType|Thang|IThangBuilder
    {

        if ($do_permission_check) {
            static::checkIfGivenIsAdmin(given: $calling_namespace,target: $given_type->owner_namespace);
        }

        if ($given_type->lifecycle === TypeOfLifecycle::PUBLISHED) {

            throw new HexbatchFailException( __('msg.type_is_already_published',['ref'=>$given_type->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_ALREADY_PUBLISHED);

        }



        $node = new static(
            given_type:$given_type,
            caller_namespace: $calling_namespace,
            do_permission_check: true
        );
        if ($do_permission_check)
        {
            $node->checkAttrForApproval();
            $node->checkParentsForApproval();
        }
        $node->checkForAbstractAttributes();

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);

        $builder->tree(
            command_class: static::class,
            command_args: (array)$node,
            command_tags: [static::class],
            command_priority: -1
        );

        if ($do_permission_check)
        {
            Evt\Server\TypePublishing::callEventsForApprovalInPublishing(given_type: $given_type,builder: $builder);
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
