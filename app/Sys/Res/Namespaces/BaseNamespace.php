<?php

namespace App\Sys\Res\Namespaces;


use App\Exceptions\HexbatchInitException;
use App\Models\UserNamespace;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemUsers;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Servers\Stock\ThisServer;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespaceCreate;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespaceEdit;
use App\Sys\Res\Users\ISystemUser;
use App\Sys\Res\Users\Stock\SystemUser;

abstract class BaseNamespace implements ISystemNamespace
{


    const TYPE_CLASS = '';
    const PUBLIC_ELEMENT_CLASS = '';
    const PRIVATE_ELEMENT_CLASS = '';
    const HOMESET_CLASS = '';
    const string SERVER_CLASS = ThisServer::class;
    const string USER_CLASS = SystemUser::class;

    public static function getFullClassName() :string {return static::class;}

    protected ?UserNamespace $namespace = null;
    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }

    public function getISystemNamespace() : ISystemNamespace {return $this;}
    public static function getHexbatchClassName() :string {
        return 'Home '. static::getSystemHomeClass()::getHexbatchClassName();
    }

    public static function getDictionaryObject() :ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid(static::class);
    }

    public static function getNamespacePublicKey(): ?string
    {
        return null;
    }


    public static function getSystemServerClass() :string|ISystemServer {
        return static::SERVER_CLASS;
    }
    public static function getSystemUserClass() :string|ISystemUser{
        return static::USER_CLASS;
    }
    public static function getSystemPublicClass() :string|ISystemElement{
        return static::PUBLIC_ELEMENT_CLASS;
    }
    public static function getSystemPrivateClass() :string|ISystemElement{
        return static::PRIVATE_ELEMENT_CLASS;
    }
    public static function getSystemHomeClass() :string|ISystemSet{
        return static::HOMESET_CLASS;
    }
    public static function getSystemTypeClass() :string|ISystemType{
        return static::TYPE_CLASS;
    }

    public function getNamespace() : UserNamespace {
        if ($this->namespace) {return $this->namespace;}
        $maybe = UserNamespace::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe) {
            $this->namespace = $maybe;
        } else {
            $this->namespace = $this->makeNamespace();
        }
        return $this->namespace;
    }

    public static function getCreatedNamespace() : UserNamespace {
        return UserNamespace::getThisNamespace(uuid: static::getClassUuid());
    }
    public function makeNamespace() :UserNamespace
   {
       try {
           $ns = new NamespaceCreate(namespace_name: static::getNamespaceName(), public_key: $this->getISystemNamespace()::getNamespacePublicKey(),
               uuid: static::getClassUuid(), given_user_uuid: static::getSystemUserClass()::getDictionaryObject()->getUserObject()?->getUuid(),
               is_stub: true, is_system: true,send_event: false);
           $ns->runAction(); //just stubbed, so no elements or sets created

           $this->b_did_create_model = true;
           return $ns->getCreatedNamespace();
       } catch (\Exception $e) {
            throw new HexbatchInitException('[makeNamespace] '.$e->getMessage(),$e->getCode(),null,$e);
       }
   }

    public function getNamespaceUser() :?ISystemUser { return SystemUsers::getSystemUserByUuid(static::USER_CLASS);}


    public function getNamespaceObject() : UserNamespace {
        return $this->getNamespace();
    }

    public function onCall(): ISystemResource
    {
        $this->getNamespaceObject();
        return $this;
    }

    public function onNextStepB(): void {}

    public function onNextStepC(): void {}
    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        //namespaces added in the home set , attribute and elements and user
        $user = $this->getISystemNamespace()->getNamespaceUser();
        if (!$user) {
            throw new HexbatchInitException('namespace next step cannot get u');
        }

        try {
            $ns = new NamespaceEdit(given_namespace_uuid: $this->getNamespace()->getUuid(),
                given_server_uuid: static::getSystemServerClass()::getDictionaryObject()->getServerObject()?->getUuid(),
                given_type_uuid: static::getSystemTypeClass()::getDictionaryObject()->getTypeObject()?->getUuid(),
                given_public_element_uuid: static::getSystemPublicClass()::getDictionaryObject()->getElementObject()?->getUuid(),
                given_private_element_uuid: static::getSystemPrivateClass()::getDictionaryObject()->getElementObject()?->getUuid(),
                given_home_set_uuid: static::getSystemHomeClass()::getDictionaryObject()->getSetObject()?->getUuid(),
                is_system: true,send_event: false
                        );
           $ns->runAction();
        } catch (\Exception $e) {
            throw new HexbatchInitException('[makeNamespace] '.$e->getMessage(),$e->getCode(),null,$e);
        }

    }

    public function __construct(
        protected bool $b_type_init = false
    ) {

    }

}
