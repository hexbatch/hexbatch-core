<?php

namespace App\Sys\Res\Users;


use App\Exceptions\HexbatchInitException;
use App\Models\User;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Us\UserRegister;

abstract class BaseSystemUser implements ISystemUser
{
    protected ?User $user = null;


    const NAMESPACE_CLASS = ThisNamespace::class;

    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }
    public static function getSystemNamespaceClass() :string|ISystemNamespace {
        return static::NAMESPACE_CLASS;
    }

    public function getISystemUser() : ISystemUser { return $this;}
    public function getUser() : User {
        if ($this->user) {return $this->user;}
        $maybe_user = User::whereRaw('users.ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe_user) {
            $this->user = $maybe_user;
        } else {
            $this->user = $this->makeUser();
        }
        return $this->user;
    }
    public function makeUser() :User
   {
       try {
           $register = new UserRegister(user_name: static::getUserName(), user_password: static::getUserPassword(),
               uuid: static::getClassUuid(), is_system: true, send_event: false);
           $register->runAction();
           $this->b_did_create_model = true;
           return $register->getCreatedUser();
       } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }

    public function getUserObject() : ?User {
        return $this->getUser();
    }

    public function getUserNamespace() :?ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid(static::getSystemNamespaceClass());
    }

    public function onCall(): ISystemResource
    {
        $this->getUserObject();
        return $this;
    }

    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        //users add in the default namespace using the uuid of the now generated ns
        $ns = $this->getISystemUser()->getUserNamespace();
        if (!$ns) {
            throw new HexbatchInitException('user next step cannot get ns');
        }
        $this->getUserObject()->default_namespace_id = $ns->getNamespaceObject()->id;
        $this->getUserObject()->save();

        //todo make a list of namespaces the user will belong to and then add that here
    }



}
