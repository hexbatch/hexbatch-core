<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Actions\Fortify\CreateNewUser;
use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\User\Params\RegistrationParamData;
use App\Enums\Sys\TypeOfAction;
use App\Helpers\NamespacePresetUuids;
use App\Models\Server;
use App\Models\User;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespaceCreate;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\DB;

#[ApiParamMarker( param_class: RegistrationParamData::class)]
class UserRegister extends Act\Cmd\Us implements ICommandCallable
{
    const string UUID = '2cca7cb0-4bde-4b66-ac54-302fba98853e';
    const TypeOfAction ACTION_NAME = TypeOfAction::CMD_USER_REGISTER;

    const ATTRIBUTE_CLASSES = [];

    const array PARENT_CLASSES = [
        Act\Cmd\Us::class,
    ];

    const array EVENT_CLASSES = [
        Evt\Server\UserRegistered::class //todo pass new default namespace to event
    ];



    public function __construct(
        protected RegistrationParamData   $params,
        protected bool $b_do_post_events = true,
    )
    {

    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'b_do_post_events'=>$this->b_do_post_events,
        ];
    }

    protected static function fromArray(array $args) : static {
        $b_do_post_events = $args['b_do_post_events']??false;
        $params = RegistrationParamData::from( $args['params']);
        return new static(params: $params,b_do_post_events: $b_do_post_events);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        $user = null;
        if ($b_approved) {
            $user = $work->doCreateUserWithDefaultNamespace();
            if ($work->b_do_post_events)
            {
                $r = new Evt\Server\UserRegistered(given_namespace: $user->default_namespace);
                $r->callTreeByItself($children_args);
            }
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'user'=>$user]);
    }


    /**
     * @throws \Throwable
     */
    public  function doCreateUserWithDefaultNamespace(bool $b_reload = true, bool $b_is_system = false, ?string $user_ref = null, ?NamespacePresetUuids $preset = null )
    : User
    {
        $created_user = null;
        DB::transaction(function() use($b_is_system,$user_ref,$preset,&$created_user)
        {
            $created_user = (new CreateNewUser)->create([
                "username" => $this->params->namespace->name,
                "password" => $this->params->password,
                "password_confirmation" => $this->params->password
            ]);
            $b_save_again = false;

            if ($user_ref ) {
                $created_user->ref_uuid = $user_ref;
                $b_save_again = true;
            }

            if ($b_is_system ) {
                $b_save_again = true;
                $created_user->is_system = $b_is_system;
            }

            if ($b_save_again) {
                $created_user->save();
            }
            $server = Server::getDefaultServer();
            $namespace_factory = new NamespaceCreate(params: $this->params->namespace,given_user: $created_user,given_server: $server,is_system: $b_is_system);
            $namespace = $namespace_factory->makeNamespace(preset: $preset);
            $created_user->default_namespace_id = $namespace->id;
            $created_user->save();

        });


        if ($b_reload) {
            $created_user = User::getThisUser(id: $created_user->id,b_relations: true);
        }

        return $created_user;
    }


    /**
     * @throws \Throwable
     */
    public static function doRegistration(
        RegistrationParamData   $params,
        ?IThangBuilder $builder = null
    ) : User|Thang|IThangBuilder
    {




        $node = new static(
            params:$params,
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();

        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class],
            command_priority: -1
        );


        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  $data['user'];
        } else {
            return $thang;
        }
    }



}

