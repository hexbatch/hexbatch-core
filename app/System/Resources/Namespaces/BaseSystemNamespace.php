<?php

namespace App\System\Resources\Namespaces;


use App\Exceptions\HexbatchInitException;
use App\Models\UserNamespace;
use App\System\Collections\SystemUsers;
use App\System\Resources\Attributes\ISystemAttribute;
use App\System\Resources\Elements\ISystemElement;
use App\System\Resources\ISystemResource;
use App\System\Resources\Servers\ISystemServer;
use App\System\Resources\Sets\ISystemSet;
use App\System\Resources\Types\ISystemType;
use App\System\Resources\Users\ISystemUser;

abstract class BaseSystemNamespace implements ISystemNamespace
{
    protected ?UserNamespace $namespace = null;
    protected ?ISystemElement $public_element = null;
    protected ?ISystemElement $private_element = null;
    protected ?ISystemSet  $home_set = null;

    protected ?ISystemAttribute  $attribute  = null;
    protected ?ISystemServer  $server  = null;
    protected ?ISystemType  $type  = null;


    const UUID = '';
    const PUBLIC_ELEMENT_UUID = '';
    const PRIVATE_ELEMENT_UUID = '';
    const HOMESET_UUID = '';
    const ATTRIBUTE_UUID = '';
    const SERVER_UUID = '';
    const USER_UUID = '';



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
    public function getNamespaceServer() :?ISystemServer { return $this->server;}
    public function getNamespaceUser() :?ISystemUser { return SystemUsers::getSystemUserByUuid(static::USER_UUID);}

    public function getPublicElement() : ?ISystemElement { return $this->public_element;}
    public function getPrivateElement() : ?ISystemElement { return $this->private_element;}
    public function getNamespaceType() : ?ISystemType { return $this->type;}
    public function getHomeSet() : ?ISystemSet { return $this->home_set;}

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
