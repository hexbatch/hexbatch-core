<?php

namespace App\Sys\Res\Types;


use App\Api\Cmd\Design\Promote\SetupForSystem;
use App\Api\Cmd\Type\AddHandle\HandleForSystem;
use App\Api\Cmd\Type\PublishPromote\PublishForSystem;
use App\Enums\Types\TypeOfLifecycle;
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
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Servers\Stock\ThisServer;

abstract class BaseType implements ISystemType
{
    protected ?ElementType $type = null;

    const UUID = '';
    const NAMESPACE_CLASS = ThisNamespace::class;

    const HANDLE_ELEMENT_CLASS = '';
    const SERVER_CLASS = ThisServer::class;

    const IS_FINAL = false;

    const TYPE_NAME = '';
    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [];

    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }

    public static function getClassUuid() : string {
        return static::UUID;
    }


    public function getClassServer() : ?ISystemServer {
        return SystemServers::getServerByUuid(static::SERVER_CLASS);
    }

    public function getType() : ElementType {
        if ($this->type) {return $this->type;}
        $maybe = ElementType::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe) {
            $this->type = $maybe;
        } else {
            $this->type = $this->makeType();
        }
        return $this->type;
    }
    public function makeType() :ElementType
   {
        if ($this->type) {return $this->type;}
        try
        {
            $sys_params = new SetupForSystem;
            $sys_params
               ->setUuid(static::getClassUuid())
               ->setTypeName(static::getClassName())
               ->setSystem(true)
               ->setFinalType(static::isFinal())
                ->setLifecycle(TypeOfLifecycle::PUBLISHED)
                ->setFinalType(static::IS_FINAL);

            $sys_params->setNamespaceId(static::getTypeNamespaceClass()::getDictionaryObject()->getNamespaceObject()?->id);
            $sys_params->setServerId(static::getTypeServerClass()::getDictionaryObject()->getServerObject()?->id);


            $what =  $sys_params->doParamsAndResponse();
            $this->b_did_create_model = true;
            return $what;

       } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }

    /**
     * @return ISystemAttribute[]
     */
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


    public static function hasInAncestors(string $target_full_class_name) :bool  {
        if (static::class === $target_full_class_name) {return true;}
        foreach (static::PARENT_CLASSES as $full_class_name) {
            if ($full_class_name === $target_full_class_name) {return true;}
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret = $full_class_name::hasInAncestors($target_full_class_name);
                if ($ret) {return true;}
            }
        }
        return false;
    }

    public static function getParentNameTree() :array  {
        $ret = [];
        $ret[static::getClassName()] = [] ;
        foreach (static::PARENT_CLASSES as $full_class_name) {
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret[static::getClassName()][] = $full_class_name::getParentNameTree();
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



    public function getHandleElement(): ?ISystemElement
    {
        return SystemElements::getElementByUuid(static::HANDLE_ELEMENT_CLASS);
    }



    public function getTypeObject() : ?ElementType {
        return $this->getType();
    }

    public function getTypeNamespace() :?ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_CLASS);
    }

    public static function getDictionaryObject() :ISystemType {
        return SystemTypes::getTypeByUuid(static::class);
    }

    public static function getClassName() :string { return static::TYPE_NAME; }
    public static function getTypeNamespaceClass() :string|ISystemNamespace { return static::NAMESPACE_CLASS; }
    public static function getTypeServerClass() :string|ISystemServer { return static::SERVER_CLASS; }
    public function isFinal(): bool { return false; }




    public function onCall(): ISystemResource
    {
        $this->getTypeObject();
        return $this;
    }

    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        try
        {
            if (static::HANDLE_ELEMENT_CLASS) {
                $sys_params = new HandleForSystem();
                $sys_params
                    ->setTypeIds([$this->getTypeObject()->id])
                    ->setHandleElementId(static::getHandleElement()->getElementObject()->id);
                $sys_params->doParamsAndResponse();
            }


            $parent_ids = [];
            foreach (static::getParentTypes() as $parent_system_object) {
                $parent_ids[] = $parent_system_object->getTypeObject()->id;
            }

            $sys_params = new PublishForSystem();
            $sys_params
                ->setTypeId($this->getTypeObject()->id)
                ->setParentIds($parent_ids)
                ->setLifecycle(TypeOfLifecycle::PUBLISHED); //at the very least, with no parents, publish this
            $sys_params->doParamsAndResponse();

        } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }
    }



}
