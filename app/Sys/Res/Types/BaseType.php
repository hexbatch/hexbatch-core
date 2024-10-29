<?php

namespace App\Sys\Res\Types;


use App\Exceptions\HexbatchInitException;
use App\Models\ElementType;
use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Servers\Stock\ThisServer;

abstract class BaseType implements ISystemType
{
    protected ?ElementType $type;

    const UUID = '';
    const NAMESPACE_CLASS = ThisServerNamespace::class;

    const HANDLE_ELEMENT_CLASS = '';
    const SERVER_CLASS = ThisServer::class;

    const TYPE_NAME = '';
    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [];

    public static function getUuid() : string {
        return static::UUID;
    }


    public function getServer() : ?ISystemServer {
        return SystemServers::getServerByUuid(static::SERVER_CLASS);
    }

    public function makeType() :?ElementType
   {
       //do not allow recursion
       $interfaces = class_implements(static::class);
       if (isset($interfaces['App\Api\Cmd\IActionWorker'])) {
           return null;
       }
       try {
           $type = new ElementType();
           return $type;
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }

    public static function getAttributeClasses() :array {
        return static::ATTRIBUTE_CLASSES;
    }

    public static function getSystemHandleElementClass() :string|ISystemElement {
        return static::HANDLE_ELEMENT_CLASS;
    }

    /** @return ISystemAttribute[] */
    public function getAttributes() :array {
        $ret = [];
        foreach (static::ATTRIBUTE_CLASSES as $class_name) {
            $ret[] = SystemAttributes::getAttributeByUuid($class_name);
        }
        return $ret;
    }

    /** @return ISystemType[] */
    public function getParentTypes() :array {
        $ret = [];
        foreach (static::PARENT_CLASSES as $class_name) {
            $ret[] = SystemTypes::getTypeByUuid($class_name);
        }
        return $ret;
    }

    public static function getParentNameTree() :array  {
        $ret = [];
        $ret[static::getName()] = [] ;
        foreach (static::PARENT_CLASSES as $full_class_name) {
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret[static::getName()][] = $full_class_name::getParentNameTree();
            }
        }
        return $ret;
    }

    public static function getFlatInheritance() : string  {
        $raw = static::renderSubtree(static::getParentNameTree());
        return preg_replace('/(\|~\|\d)/', "\n   ",$raw);
    }

    public static function renderSubtree(array $tree) : string  {
        $ret = [];

        $count = 0;
        foreach ($tree as $k => $v) {
            if ($count) {
                $ret[] = '~';
            }
            if ($k) {
                $ret[] = $k;
            }

            if (count($v) ) {
                $what = static::renderSubtree($v);
                $ret[] = $what;

            }
            $count++;

        }


        return implode('|',$ret);
    }
/*
 *
 */



    public function getDescriptionElement(): ?ISystemElement
    {
        return SystemElements::getElementByUuid(static::HANDLE_ELEMENT_CLASS);
    }



    public function getTypeObject() : ?ElementType {
        if ($this->type) {return $this->type;}
        $this->type = $this->makeType();
        return $this->type;
    }

    public function getTypeNamespace() :?ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_CLASS);
    }

    public function getTypeName(): string { return static::TYPE_NAME;}
    public static function getName() :string { return static::TYPE_NAME; }
    public function isFinal(): bool { return false; }




    public function onCall(): ISystemResource
    {
        $this->getTypeObject();
        return $this;
    }

    public function onNextStep(): void
    {
        //users add in the default namespace using the uuid of the now generated ns
        $ns = $this->getTypeNamespace();
        if (!$ns) {
            throw new HexbatchInitException('type next step cannot get ns');
        }
        $this->getTypeObject()->owner_namespace_id = $ns->getNamespaceObject()->id;
        $this->getTypeObject()->save();
    }



}
