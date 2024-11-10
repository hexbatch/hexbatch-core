<?php

namespace App\Sys\Res\Users;


use App\Actions\Fortify\CreateNewUser;
use App\Exceptions\HexbatchInitException;
use App\Models\User;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;

abstract class BaseSystemUser implements ISystemUser
{
    protected ?User $user = null;


    const NAMESPACE_CLASS = ThisNamespace::class;


    public static function getSystemNamespaceClass() :string|ISystemNamespace {
        return static::NAMESPACE_CLASS;
    }


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
           $user = (new CreateNewUser)->create([
               "username" => static::getUserName(),
               "password" => static::getUserPassword(),
               "password_confirmation" => static::getUserPassword()
           ]);
           $user->ref_uuid = static::getClassUuid();
           $user->is_system = true;
           $user->save();
           $user->refresh();
           return $user;
       } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }

    public function getUserObject() : ?User {
        return $this->getUser();
    }

    public function getUserNamespace() :?ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_CLASS);
    }

    public function onCall(): ISystemResource
    {
        $this->getUserObject();
        return $this;
    }

    public function onNextStep(): void
    {
        //users add in the default namespace using the uuid of the now generated ns
        $ns = $this->getUserNamespace();
        if (!$ns) {
            throw new HexbatchInitException('user next step cannot get ns');
        }
        $this->getUserObject()->default_namespace_id = $ns->getNamespaceObject()->id;
        $this->getUserObject()->save();
    }



}
