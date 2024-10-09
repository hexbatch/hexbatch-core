<?php

namespace App\Sys\Res\Namespaces;


use App\Models\UserNamespace;
use App\Sys\Res\Elements\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Users\ISystemUser;

interface ISystemNamespace extends ISystemResource
{
    public function getNamespaceUuid() :string;
    public function getNamespaceName() :string;
    public function getNamespacePublicKey() :string;
    public function getNamespaceServer() :?ISystemServer;
    public function getNamespaceUser() :?ISystemUser;

    public function getPublicElement() : ?ISystemElement ;
    public function getPrivateElement() : ?ISystemElement ;
    public function getNamespaceType() : ?ISystemType ;
    public function getHomeSet() : ?ISystemSet ;

    public function getNamespaceObject() : UserNamespace;

}
