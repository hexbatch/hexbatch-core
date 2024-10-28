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
use App\Sys\Res\Servers\Stock\ThisServer;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Users\ISystemUser;
use App\Sys\Res\Users\Stock\SystemUser;

abstract class BaseNamespace implements ISystemNamespace
{


    const UUID = '';
    const TYPE_CLASS = '';
    const PUBLIC_ELEMENT_CLASS = '';
    const PRIVATE_ELEMENT_CLASS = '';
    const HOMESET_CLASS = '';
    const SERVER_CLASS = ThisServer::class;
    const USER_CLASS = SystemUser::class;

    protected ?UserNamespace $namespace = null;

    public static function getUuid() : string {
        return static::UUID;
    }

    public static function getNamespacePublicKey(): ?string
    {
        return null;
    }


    public static function getSystemServerClass() :string|ISystemServer {
        return static::SERVER_CLASS;
    }
    public static function getSystemUserClass() :string|ISystemUser{
        return static::USER_CLASS;
    }
    public static function getSystemPublicClass() :string|ISystemElement{
        return static::PUBLIC_ELEMENT_CLASS;
    }
    public static function getSystemPrivateClass() :string|ISystemElement{
        return static::PRIVATE_ELEMENT_CLASS;
    }
    public static function getSystemHomeClass() :string|ISystemSet{
        return static::HOMESET_CLASS;
    }
    public static function getSystemTypeClass() :string|ISystemType{
        return static::TYPE_CLASS;
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

    public function getNamespaceServer() :?ISystemServer { return SystemServers::getServerByUuid(static::SERVER_CLASS);}
    public function getNamespaceUser() :?ISystemUser { return SystemUsers::getSystemUserByUuid(static::USER_CLASS);}

    public function getPublicElement() : ?ISystemElement { return SystemElements::getElementByUuid(static::PUBLIC_ELEMENT_CLASS);}
    public function getPrivateElement() : ?ISystemElement { return SystemElements::getElementByUuid(static::PRIVATE_ELEMENT_CLASS);}
    public function getNamespaceType() : ?ISystemType { return SystemTypes::getTypeByUuid(static::TYPE_CLASS);}
    public function getHomeSet() : ?ISystemSet { return SystemSets::getSetByUuid(static::TYPE_CLASS);}

    public function getNamespaceObject() : UserNamespace {
        if ($this->namespace) {return $this->namespace;}
        $this->namespace = $this->makeNamespace();
        // todo but need to make that system namespace first, even without any data except the uuid
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
        //todo fill in the other stuff for the sys ns
        $this->getNamespaceObject()->save();
    }



}
