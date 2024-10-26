<?php

namespace App\Sys\Res\Users;


use App\Actions\Fortify\CreateNewUser;
use App\Exceptions\HexbatchInitException;
use App\Models\User;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;

abstract class BaseSystemUser implements ISystemUser
{
    protected ?User $user;


    const NAMESPACE_CLASS = ThisServerNamespace::class;
    

    public static function getSystemNamespaceClass() :string|ISystemNamespace {
        return static::NAMESPACE_CLASS;
    }


    public function makeUser() :User
   {
       try {
           $user = (new CreateNewUser)->create([
               "username" => static::getUserName(),
               "password" => static::getUserPassword(),
               "password_confirmation" => static::getUserPassword()
           ]);
           $user->ref_uuid = static::getUuid();
           $user->save();
           $user->refresh();
           return $user;
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }

    public function getUserObject() : ?User {
        if ($this->user) {return $this->user;}
        $this->user = $this->makeUser();
        return $this->user;
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
