<?php

namespace App\Sys\Res\Namespaces;


use App\Exceptions\HexbatchInitException;
use App\Models\UserNamespace;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemServers;
use App\Sys\Collections\SystemSets;
use App\Sys\Collections\SystemTypes;
use App\Sys\Collections\SystemUsers;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Users\ISystemUser;

 abstract class BaseNamespace implements ISystemNamespace
{


    const UUID = '';
    const TYPE_UUID = '';
    const PUBLIC_ELEMENT_UUID = '';
    const PRIVATE_ELEMENT_UUID = '';
    const HOMESET_UUID = '';
    const SERVER_UUID = '';
    const USER_UUID = '';

    protected ?UserNamespace $namespace = null;

     public function getUuid() : string {
         return static::UUID;
     }


    public function makeNamespace() :UserNamespace
   {
       try {
           $ret = new UserNamespace();

           return $ret;
       } catch (\Exception $e) {
            throw new HexbatchInitException('[makeNamespace] '.$e->getMessage(),$e->getCode(),null,$e);
       }
   }

    public function getNamespaceUuid() :string {return static::UUID;}
    public function getNamespaceServer() :?ISystemServer { return SystemServers::getServerByUuid(static::SERVER_UUID);}
    public function getNamespaceUser() :?ISystemUser { return SystemUsers::getSystemUserByUuid(static::USER_UUID);}

    public function getPublicElement() : ?ISystemElement { return SystemElements::getElementByUuid(static::PUBLIC_ELEMENT_UUID);}
    public function getPrivateElement() : ?ISystemElement { return SystemElements::getElementByUuid(static::PRIVATE_ELEMENT_UUID);}
    public function getNamespaceType() : ?ISystemType { return SystemTypes::getTypeByUuid(static::TYPE_UUID);}
    public function getHomeSet() : ?ISystemSet { return SystemSets::getSetByUuid(static::TYPE_UUID);}

    public function getNamespaceObject() : UserNamespace {
        if ($this->namespace) {return $this->namespace;}
        $this->namespace = $this->makeNamespace();
        return $this->namespace;
    }

    public function onCall(): ISystemResource
    {
        $this->getNamespaceObject();
        return $this;
    }

    public function onNextStep(): void
    {
        //namespaces added in the home set , attribute and elements and user
        $user = $this->getNamespaceUser();
        if (!$user) {
            throw new HexbatchInitException('namespace next step cannot get u');
        }

        $this->getNamespaceObject()->namespace_user_id = $user->getUserObject()->id;
        $this->getNamespaceObject()->save();
    }



}
