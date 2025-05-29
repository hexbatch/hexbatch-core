<?php

namespace App\Sys\Res\Namespaces;


use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Users\ISystemUser;

interface ISystemNamespace extends ISystemResource, INamespace
{

    public static function getDictionaryObject() :ISystemNamespace;
    public static function getSystemServerClass() :string|ISystemServer;
    public static function getSystemUserClass() :string|ISystemUser;
    public static function getSystemPublicClass() :string|ISystemElement;
    public static function getSystemPrivateClass() :string|ISystemElement;
    public static function getSystemHomeClass() :string|ISystemSet;
    public static function getSystemTypeClass() :string|ISystemType;

    public static function getNamespaceName() :string;
    public static function getNamespacePublicKey() :?string;
    public function getNamespaceUser() :?ISystemUser;

    public function getISystemNamespace() : ISystemNamespace;




}
