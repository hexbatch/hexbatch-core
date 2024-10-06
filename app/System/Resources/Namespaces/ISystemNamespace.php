<?php

namespace App\System\Resources\Namespaces;


use App\System\Resources\Attributes\ISystemAttribute;
use App\System\Resources\Elements\ISystemElement;
use App\System\Resources\Servers\ISystemServer;
use App\System\Resources\Sets\ISystemSet;
use App\System\Resources\Types\ISystemType;
use App\System\Resources\Users\ISystemUser;

interface ISystemNamespace
{
    public function getNamespaceUuid() :string;
    public function getNamespaceName() :string;
    public function getNamespacePublicKey() :string;
    public function getNamespaceServer() :ISystemServer;
    public function getNamespaceUser() :ISystemUser;

    public function getPublicElement() : ISystemElement ;
    public function getPrivateElement() : ISystemElement ;
    public function getNamespaceType() : ISystemType ;
    public function getBaseAttribute() : ISystemAttribute ;
    public function getHomeSet() : ISystemSet ;

}
