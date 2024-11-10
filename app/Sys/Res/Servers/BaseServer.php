<?php

namespace App\Sys\Res\Servers;


use App\Api\Cmd\Server\Promote\ServerForSystem;
use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\Models\Server;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;


abstract class BaseServer implements ISystemServer
{
    protected ?Server $server = null;

    const NAMESPACE_CLASS = '';
    const SERVER_TYPE_CLASS = '';

    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }

    public static function getDictionaryObject() :ISystemServer {
        return SystemServers::getServerByUuid(static::class);
    }

    public static function getSystemNamespaceClass(): string|ISystemNamespace {
        return static::NAMESPACE_CLASS;
    }

    public static function getSystemTypeClass(): string|ISystemType {
        return static::SERVER_TYPE_CLASS;
    }

    public function getServerSystemNamespace(): ISystemNamespace
    {
      return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_CLASS);
    }

    public function getServer() : Server {
        if ($this->server) {return $this->server;}
        $maybe = Server::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe) {
            $this->server = $maybe;
        } else {
            $this->server = $this->makeServer();
        }
        return $this->server;
    }

    public function makeServer() :Server
   {
       if ($this->server) {return $this->server;}
       try
       {
           $sys_params = new ServerForSystem();
           $sys_params
               ->setUuid(static::getClassUuid())
               ->setSystem(true)
               ->setServerName(static::getServerName())
               ->setServerUrl(static::getServerUrl())
               ->setServerDomain(static::getServerDomain())
               ->setServerAccessToken(null)
               ->setAccessTokenExpiresAt(null)
               ->setServerStatus(TypeOfServerStatus::ALLOWED_SERVER)

               ;

           $what =  $sys_params->doParamsAndResponse();
           $this->b_did_create_model = true;
           return $what;

       } catch (\Exception $e) {
           throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }




    public function getServerObject() : ?Server {
        return $this->getServer();
    }



    public function onCall(): ISystemResource
    {
        $this->getServerObject();
        return $this;
    }

    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        $sys_params = new ServerForSystem();
        $sys_params
            ->setServerId($this->server->id)
            ->setServerName(static::getServerName())
            ->setServerTypeId(static::getSystemTypeClass()::getDictionaryObject()->getTypeObject()?->id)
            ->setOwningNamespaceId(static::getSystemNamespaceClass()::getDictionaryObject()->getNamespaceObject()?->id);

         $sys_params->doParamsAndResponse();
    }

    public function getServerNamespaceInterface() :?INamespace {
      return $this->getServerObject()?->owning_namespace;
    }


  }
