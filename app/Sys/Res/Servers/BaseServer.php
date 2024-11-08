<?php

namespace App\Sys\Res\Servers;


use App\Api\Cmd\Server\Promote\ServerForSystem;
use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\Models\Server;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;


abstract class BaseServer implements ISystemServer
{
    protected ?Server $server;

    const NAMESPACE_CLASS = '';
    const SERVER_TYPE_CLASS = '';



    public static function getSystemNamespaceClass(): string|ISystemNamespace {
        return static::NAMESPACE_CLASS;
    } public static function getSystemTypeClass(): string|ISystemType {
        return static::SERVER_TYPE_CLASS;
    }

  public function getServerSystemNamespace(): ISystemNamespace
  {
      return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_CLASS);
  }


    public function makeServer() :Server
   {
       if ($this->server) {return $this->server;}
       try
       {
           $sys_params = new ServerForSystem();
           $sys_params
               ->setUuid(static::getClassUuid())
               ->setServerName(static::getServerName())
               ->setServerDomain(static::getServerDomain())
               ->setServerAccessToken(null)
               ->setAccessTokenExpiresAt(null)
               ->setServerStatus(TypeOfServerStatus::ALLOWED_SERVER)
               ->setServerTypeId(static::getSystemTypeClass()->getTypeObject()?->id)
               ->setOwningNamespaceId(static::getSystemNamespaceClass()->getNamespaceObject()?->id)
               ;

           return $sys_params->doParamsAndResponse();

       } catch (\Exception $e) {
           throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }




    public function getServerObject() : ?Server {
        if ($this->server) {return $this->server;}
        $this->server = $this->makeServer();
        return $this->server;
    }



    public function onCall(): ISystemResource
    {
        $this->getServerObject();
        return $this;
    }

    public function onNextStep(): void
    {

    }

    public function getServerNamespaceInterface() :?INamespace {
      return $this->getServerObject()?->owning_namespace;
    }


  }
