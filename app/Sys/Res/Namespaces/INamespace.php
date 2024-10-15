<?php

namespace App\Sys\Res\Namespaces;


use App\Models\UserNamespace;

interface INamespace
{
    public function getNamespaceUuid() :string;
    public function getNamespaceName() :string;

    public function getNamespaceObject() : UserNamespace;

}
