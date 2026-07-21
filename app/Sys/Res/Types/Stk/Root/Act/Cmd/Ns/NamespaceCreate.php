<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\NamespaceParamData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Data\ApiParams\Data\Types\Params\TypeParamData;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Helpers\NamespacePresetUuids;
use App\Models\ElementType;
use App\Models\ElementTypeParent;
use App\Models\Phase;
use App\Models\Server;
use App\Models\User;
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


#[ApiParamMarker( param_class: NamespaceParamData::class)]
class NamespaceCreate extends Act\Cmd\Ns implements ICommandCallable
{
    const UUID = '2eb062ae-f06e-4b01-8a9f-2059f2fbc40b';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceCreated::class
    ];


    public function __construct(
        protected NamespaceParamData $params,
        protected User $given_user,
        protected Server $given_server,
        protected bool   $is_system,
    )
    {

    }


    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'is_system'=> $this->is_system,
            'given_server'=> $this->given_server,
            'given_user'=> $this->given_user
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = null;
        if (!empty($args['params']??null)) {
            $params = NamespaceParamData::from($args['params']);
        }

        $is_system = (bool)$args['is_system'];
        $given_user = static::getUserFromArray('given_user',$args);
        $given_server = static::getServerFromArray('given_server',$args);

        return new static(
            params: $params,
            given_user: $given_user, given_server: $given_server,
            is_system: $is_system,
           );
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        $namespace = null;
        if ($b_approved) {
            $namespace = $work->makeNamespace(b_refresh_with_dependencies: true);

            if (!$work->is_system)
            {
                $r = new Evt\Server\NamespaceCreated(given_type: $namespace->namespace_base_type,given_namespace: $namespace);
                $r->callTreeByItself($children_args);
            }

        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'namespace'=>$namespace]);
    }

    /**
     * @throws \Throwable
     * does not reload ns after attaching stuff together
     */
    public function makeNamespace(
        ?NamespacePresetUuids $preset = null,
        bool $b_refresh_with_dependencies = false
    ) : UserNamespace
    {
        $created_namespace = null;
        DB::transaction(function() use(
            &$created_namespace,
            $preset
        )
        {



            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            $base_type = null;
            if ($preset->base_type_uuid) {
                $base_type = ElementType::where('ref_uuid',$preset->base_type_uuid);
            }
            if (!$base_type)
            {
                // make base type
                $base_type_params = TypeParamData::from([
                    'type_name'=> $this->params->name . static::BASE_TYPE_POSTFIX,
                    'is_final_type'=> false,
                    'access'=> TypeOfServerAccess::IS_PUBLIC,
                ]);
                $base_type_factory = new Act\Cmd\Ds\DesignCreate(
                    params: $base_type_params,
                    is_system: $this->is_system,
                    use_ref: $preset->base_type_uuid,
                    owner_namespace: null,
                    server: $this->given_server
                );

                $base_type = $base_type_factory->createDesign();
                ElementTypeParent::addOrUpdateParent(parent: ElementType::getNamespaceBaseType(), child: $base_type);
                $base_type = new Act\Cmd\Ty\TypePublish(given_type: $base_type, caller_namespace: null, do_permission_check: false)->doPublishCall();

            }


            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            // make home type
            $home_type_params = TypeParamData::from([
                'type_name'=> $this->params->name . static::HOME_TYPE_POSTFIX,
                'is_final_type'=> false,
                'access'=> TypeOfServerAccess::IS_PUBLIC,
            ]);

            $home_type_factory = new Act\Cmd\Ds\DesignCreate(
                params: $home_type_params,
                is_system: $this->is_system,
                use_ref: $preset->home_type_uuid,
                owner_namespace: null,
                server: $this->given_server
            );

            $home_type = $home_type_factory->createDesign();
            ElementTypeParent::addOrUpdateParent(parent: $base_type, child: $home_type);
            ElementTypeParent::addOrUpdateParent(parent: ElementType::getNamespaceSetType(), child: $home_type);
            $home_type = new Act\Cmd\Ty\TypePublish(given_type: $home_type, caller_namespace: null, do_permission_check: false)->doPublishCall();


            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            // make public type
            $home_type_params = TypeParamData::from([
                'type_name'=> $this->params->name . static::PUBLIC_TYPE_POSTFIX,
                'is_final_type'=> false,
                'access'=> TypeOfServerAccess::IS_PUBLIC,
            ]);

            $public_type_factory = new Act\Cmd\Ds\DesignCreate(
                params: $home_type_params,
                is_system: $this->is_system,
                use_ref: $preset->public_type_uuid,
                owner_namespace: null,
                server: $this->given_server
            );

            $public_type = $public_type_factory->createDesign();
            ElementTypeParent::addOrUpdateParent(parent: $base_type, child: $public_type);
            ElementTypeParent::addOrUpdateParent(parent: ElementType::getNamespacePublicType(), child: $public_type);
            $public_type = new Act\Cmd\Ty\TypePublish(given_type: $public_type, caller_namespace: null, do_permission_check: false)->doPublishCall();


            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            // make private type
            $private_type_params = TypeParamData::from([
                'type_name'=> $this->params->name . static::PRIVATE_TYPE_POSTFIX,
                'is_final_type'=> false,
                'access'=> TypeOfServerAccess::IS_ELEMENT_PRIVATE,
            ]);

            $private_type_factory = new Act\Cmd\Ds\DesignCreate(
                params: $private_type_params,
                is_system: $this->is_system,
                use_ref: $preset->private_type_uuid,
                owner_namespace: null,
                server: $this->given_server
            );

            $private_type = $private_type_factory->createDesign();
            ElementTypeParent::addOrUpdateParent(parent: $base_type, child: $private_type);
            ElementTypeParent::addOrUpdateParent(parent: ElementType::getNamespacePrivateType(), child: $private_type);
            $private_type = new Act\Cmd\Ty\TypePublish(given_type: $private_type, caller_namespace: null, do_permission_check: false)->doPublishCall();


            $phase = Phase::getDefaultPhase();
            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            //make element for home

            $home_type_element_factory = new Act\Cmd\Ty\ElementCreate(element_type: $home_type, phase: $phase, number_to_create: 1,
                owner_namespace: null, is_system: $this->is_system, calling_namespace: null,
                preassinged_uuids: ($preset->home_element_uuid)? [$preset->home_element_uuid]: []
            );
            $home_element = $home_type_element_factory->makeElement(b_do_refresh: false);


            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            //make private element

            $private_type_element_factory = new Act\Cmd\Ty\ElementCreate(element_type: $private_type, phase: $phase, number_to_create: 1,
                owner_namespace: null, is_system: $this->is_system, calling_namespace: null,
                preassinged_uuids: ($preset->private_element_uuid)? [$preset->private_element_uuid]: []
            );
            $private_element = $private_type_element_factory->makeElement(b_do_refresh: false);


            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            //make public element

            $public_type_element_factory = new Act\Cmd\Ty\ElementCreate(element_type: $public_type, phase: $phase, number_to_create: 1,
                owner_namespace: null, is_system: $this->is_system, calling_namespace: null,
                preassinged_uuids: ($preset->public_element_uuid)? [$preset->public_element_uuid]: []
            );
            $public_element = $public_type_element_factory->makeElement(b_do_refresh: false);



            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            # make home set

            $home_set_factory = new Act\Cmd\Ele\SetCreate(defining_element: $home_element,has_events: true,is_system: $this->is_system,calling_namespace: null,
                preassinged_uuid: $preset->home_set_uuid);

            $home_set = $home_set_factory->doCreateSet(b_do_refresh: false);


            # ───── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ────────── ⋆⋅☆⋅⋆ ─────
            # add public and private to home set
            $add_to_set_factory = new Act\Cmd\St\SetMemberAdd(params: null,given_set: $home_set,is_system: $this->is_system,calling_namespace: null,
                selected_elements: collect([$public_element,$private_element]) );

            $add_to_set_factory->addElements(b_do_refresh: false,b_sticky_override: true);



            $created_namespace = UserNamespace::createNamespace(
                namespace_name: $this->params->name, owning_user_id: $this->given_user->id,
                server_id: $this->given_server->id, ref: $preset->namespace_uuid,
                type_id: $base_type->id,
                public_element_id: $public_element->id,
                private_element_id: $private_element->id,
                home_set_id: $home_set->id,
                public_key: $this->params->public_key, is_system: $this->is_system
            );
            $public_element->element_namespace_id = $created_namespace->id;
            $public_element->save();

            $public_element->element_parent_type->owner_namespace_id = $created_namespace->id;
            $public_element->element_parent_type->save();

            $private_element->element_namespace_id = $created_namespace->id;
            $private_element->save();

            $private_element->element_parent_type->owner_namespace_id = $created_namespace->id;
            $private_element->element_parent_type->save();

            $home_set->defining_element->element_namespace_id = $created_namespace->id;
            $home_set->defining_element->save();

            $home_set->defining_element->element_parent_type->owner_namespace_id = $created_namespace->id;
            $home_set->defining_element->element_parent_type->save();

            $base_type->owner_namespace_id = $created_namespace->id;
            $base_type->save();
        });

        if ($created_namespace && $b_refresh_with_dependencies) {
            $created_namespace->refresh();
            $created_namespace->loadMissing('home_set','public_element','private_element','namespace_base_type');
        }
        return $created_namespace;
    }

    const BASE_TYPE_POSTFIX = '_base';

    const PUBLIC_TYPE_POSTFIX = '_public';

    const PRIVATE_TYPE_POSTFIX = '_private';

    const HOME_TYPE_POSTFIX = '_home';


    /**
     * @throws \Throwable
     */
    public static function makeCreateNamespaceTree(
         NamespaceParamData $params,
         User|null $given_user,
         ?Server $given_server,
         bool                    $is_system,
         ?UserNamespace           $calling_namespace,
         ?IThangBuilder $builder = null
    ) : UserNamespaceData|Thang|IThangBuilder
    {

        if (!$given_user && !$calling_namespace) {
            throw new \LogicException("Need a calling namespace or a user here");
        } else if(!$given_user) {
            $given_user = $calling_namespace->owner_user;
        }

        if (!$given_server) {$given_server = Server::getDefaultServer();}
        $node = new static(
            params: $params,
            given_user:$given_user,
            given_server:$given_server,
            is_system: $is_system
        );

        $ret_builder = false;
        if ($builder) { $ret_builder = true;}

        $builder?: $builder = ThangBuilder::createBuilder();

        $builder->tree(
            command_class: static::class,
            command_args: (array)$node,
            command_tags: [static::class]
        );


        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  UserNamespaceData::from($data['namespace']);
        } else {
            return $thang;
        }
    }


}

