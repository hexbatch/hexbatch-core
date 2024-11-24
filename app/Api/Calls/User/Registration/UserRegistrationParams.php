<?php
namespace App\Api\Calls\User\Registration;



use App\Api\Calls\IApiThingSetup;

use App\Api\IApiOaParams;
use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Api;

use App\Api\Cmd;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'UserRegistartionParams' )]
class UserRegistrationParams extends Api\User\UserRegister implements IApiOaParams, IApiThingSetup
{

    #[OA\Property(title: 'New User')]
    protected Cmd\Users\Create\UserCreateParams $new_user;

    protected Cmd\Namespace\Promote\NamespacePromoteParams $namespace_params;

    public function fromCollection(Collection $collection)
    {
        //todo make a new action param for each action, and have them be class members, and call their from collection
        // but if only using data from a previous action, how to struture that?
    }



    public function pushData(Thing $thing): void
    {
        /*
         *todo The api params will write to the thing data, if it wants to, and then for each of its member actions make a new thing child or descendant, and
         * call that action's params pushData so it can do its setup data,
         * the caller will then change  the leafs as pending and all else as waiting on child
         * but if no data yet to push for pending actions that rely on actions yet to run, how to do that?

         */
    }


    public function getActions(): array
    {
        return [
            Cmd\Users\Create\UserCreateParams::class,
            Cmd\Namespace\Promote\NamespacePromoteParams::class,
            //todo edit new user, set that ns as the default
        ];
    }


}
