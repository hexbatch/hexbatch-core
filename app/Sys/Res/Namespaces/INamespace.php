<?php

namespace App\Sys\Res\Namespaces;


use App\Models\UserNamespace;

interface INamespace
{


    public function getNamespaceObject() : UserNamespace;

}
