<?php

namespace App\Sys\Res\Servers;


use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\Models\Server;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Server\ServerPromote;


abstract class BaseServer implements ISystemServer
{
    protected ?Server $server = null;

    const NAMESPACE_CLASS = '';
    const SERVER_TYPE_CLASS = '';

    public static function getFullClassName() :string {return static::class;}

    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }

    public static function getDictionaryObject() :ISystemServer {
        return SystemServers::getServerByUuid(static::class);
    }

    public function getISystemServer(): ISystemServer {return $this;}

    public static function getSystemNamespaceClass(): string|ISystemNamespace {
        return static::NAMESPACE_CLASS;
    }

    public static function getSystemTypeClass(): string|ISystemType {
        return static::SERVER_TYPE_CLASS;
    }

    public function getServerSystemNamespace(): ISystemNamespace
    {
      return SystemNamespaces::getNamespaceByUuid(static::getSystemNamespaceClass());
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
           $creator = new ServerPromote(
             given_type_uuid:   $this->getISystemServer()::getSystemTypeClass()::getDictionaryObject()->getTypeObject()->getUuid(),
             given_namespace_uuid:  $this->getISystemServer()->getServerSystemNamespace()?->getNamespaceObject()?->getUuid(),
             server_name:   static::getServerName(),
               server_domain: static::getServerDomain(),
               server_url: static::getServerUrl(),
               server_status: TypeOfServerStatus::ALLOWED_SERVER,
               uuid: static::getClassUuid(),is_system: true,send_event: false
           );

           $creator->runAction();
           $what =  $creator->getCreatedServer();
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

    public function onNextStepB(): void {}


    public function onNextStepC(): void {}

    public function onNextStep(): void
    {

    }


    public function __construct(
        protected bool $b_type_init = false
    ) {

    }

  }
