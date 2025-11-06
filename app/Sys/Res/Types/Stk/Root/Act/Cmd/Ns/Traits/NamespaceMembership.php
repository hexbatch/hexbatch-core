<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\Traits;

use App\Data\ApiParams\Data\Namespaces\NamespaceMemberData;
use App\Data\ApiParams\Data\Namespaces\Params\NamespaceSelectionParamData;
use App\Data\ApiParams\Data\Namespaces\Responses\NamespaceMemberListData;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\UserNamespace;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

trait NamespaceMembership
{
    public function __construct(
        protected NamespaceSelectionParamData $params,
        protected UserNamespace           $target_namespace,
        protected UserNamespace           $calling_namespace,
        protected bool           $do_permission_check = true,
    )
    {

    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'target_namespace'=> $this->target_namespace,
            'calling_namespace'=> $this->calling_namespace,
            'do_permission_check'=> $this->do_permission_check,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = null;
        if (!empty($args['params']??null)) {
            $params = NamespaceSelectionParamData::makingUsingCodeArray($args['params']);
        }
        $target_namespace = static::getNamespaceFromArray('target_namespace',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $do_permission_check = (bool)$args['do_permission_check'];

        return new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace,
            do_permission_check: $do_permission_check
        );
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        $info = null;
        if ($b_approved) {
            $info = $work->doAction();

            $event = static::POST_EVENT;
            $r = new $event(given_element: $work->target_namespace->private_element, given_set: $work->target_namespace->home_set);
            $r->callTreeByItself($children_args);
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL, data: ['results'=>$info]);
    }

    /**
     * @throws \Throwable
     * does not reload ns after attaching stuff together
     */
    public function doAction(

    ) : NamespaceMemberListData
    {
        /** @var UserNamespace[] $namespaces */
        $namespaces = [];
        foreach ($this->params->namespace_refs??[] as $ns_ref) {
            $namespaces[] = UserNamespace::resolveNamespace(value: $ns_ref,b_relations: true);
        }

        if (empty($namespaces)) {
            return NamespaceMemberListData::makingUsingCodeArray(['data'=>collect()]);
        }

        if ($this->do_permission_check)
        {
            if (static::IS_ADMIN) {
                static::checkIfGivenIsOwner(given: $this->calling_namespace,target: $this->target_namespace);
            } else {
                static::checkIfGivenIsAdmin(given: $this->calling_namespace,target: $this->target_namespace);
            }

        }

        /** @var Collection<NamespaceMemberData> $member_list */
        $member_list = new Collection;
        DB::transaction(function () use($namespaces,$member_list)
        {
            foreach ($namespaces as $ns)
            {
                $member = $this->target_namespace->isNamespaceMember(namespace: $ns );
                if (static::IS_ADDING ) {

                    if(static::IS_ADMIN) {
                        //add as admin or promote existing
                        if (!$member) {
                            $member = $this->target_namespace->addMember(child: $ns,is_admin: true );
                            $memberData = NamespaceMemberData::makingUsingCodeArray($member);
                            $member_list->add($memberData);
                        } else if (!$member->is_admin) {
                            $member->is_admin = true;
                            $member->save();
                            $memberData = NamespaceMemberData::makingUsingCodeArray($member);
                            $member_list->add($memberData);
                        }
                    } else {
                        //add as regular member, only if not existing
                        if (!$member) {
                            $member = $this->target_namespace->addMember(child: $ns );
                            $memberData = NamespaceMemberData::makingUsingCodeArray($member);
                            $member_list->add($memberData);
                        }
                    }
                } else  {
                    //is removing
                    if ($this->target_namespace->id === $ns->id) {
                        throw new HexbatchPermissionException(__("msg.namespace_cannot_demote_owner",['ref'=>$this->target_namespace->getName()]),
                            Response::HTTP_FORBIDDEN,
                            RefCodes::NAMESPACE_CANNOT_REMOVE_OWNER_AS_ADMIN);

                    }
                    if(static::IS_ADMIN) {
                        //removing admin status, but not membership
                        if ($member?->is_admin) {
                            $member->is_admin = false;
                            $member->save();
                            $memberData = NamespaceMemberData::makingUsingCodeArray($member);
                            $member_list->add($memberData);
                        }
                    } else {
                        //removing membership, but not admins
                        if ($member && !$member->is_admin) {
                            $this->target_namespace->removeMember(child: $ns );
                            $memberData = NamespaceMemberData::makingUsingCodeArray($member);
                            $member_list->add($memberData);
                        }
                    }

                }

            }
        });
        return NamespaceMemberListData::makingUsingCodeArray(['data'=>$member_list]);
    }



    /**
     * @throws \Throwable
     */
    public static function makeTree(
        NamespaceSelectionParamData $params,
        ?UserNamespace           $calling_namespace,
        ?UserNamespace           $target_namespace,
        bool $do_permission_check = true,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {

        if ($do_permission_check) {

            if ($target_namespace->namespace_user_id !== $calling_namespace->namespace_user_id) {
                throw new HexbatchPermissionException(__("msg.namespace_not_owner",['ref'=>$target_namespace->getName()]),
                    Response::HTTP_FORBIDDEN,
                    RefCodes::NAMESPACE_NOT_OWNER);
            }

        }

        //check for default ns
        $target_namespace->loadMissing('owner_user');
        if ($target_namespace->owner_user->default_namespace_id === $target_namespace->namespace_user_id) {
            throw new HexbatchPermissionException(__("msg.namespace_cannot_delete_default",
                ['ref'=>$target_namespace->getName(),'user_name'=>$target_namespace->owner_user->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_CANNOT_DELETE_CORE_PARTS);
        }


        $node = new static(
            params: $params,
            target_namespace: $target_namespace,
            calling_namespace: $calling_namespace,
            do_permission_check: $do_permission_check
        );

        $ret_builder = false;
        if ($builder) { $ret_builder = true;}

        $builder?: $builder = ThangBuilder::createBuilder();

        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class]
        );


        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();
    }
}
