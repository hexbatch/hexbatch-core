<?php

namespace App\System\Resources\Servers;


use App\Exceptions\HexbatchInitException;
use App\Models\Server;
use App\System\Collections\SystemNamespaces;
use App\System\Resources\ISystemResource;
use App\System\Resources\Namespaces\ISystemNamespace;


abstract class BaseServer implements ISystemServer
{
    protected ?Server $server;



  public function getServerNamespace(): ISystemNamespace
  {
      return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_UUID);
  }

    public function getServerUuid() :string { return static::UUID;}

    public function makeServer() :Server
   {
       try {
           $server = new Server();
           return $server;
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


  }
